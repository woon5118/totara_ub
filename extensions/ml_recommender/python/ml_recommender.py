"""
This file is part of Totara Enterprise Extensions.

Copyright (C) 2020 onwards Totara Learning Solutions LTD

Totara Enterprise Extensions is provided only to Totara
Learning Solutions LTD's customers and partners, pursuant to
the terms and conditions of a separate agreement with Totara
Learning Solutions LTD or its affiliate.

If you do not have an agreement with Totara Learning Solutions
LTD, you may not access, use, modify, or distribute this software.
Please contact [licensing@totaralearning.com] for more information.

@author Vernon Denny <vernon.denny@totaralearning.com>
@package ml_recommender
"""

"""
TODO:
-----
    Look into compiling python to cython - is there enough gain to make it worthwhile?
    Totara settings form for external parameters
    Documentation of scripts, settings & parameters:
            System schematic/diagram
            What settings & parameters mean
            Why the defaults are what they are
            What kinds of tweaking makes sense

"""

import argparse
from collections import Counter
import itertools
import json
import langid
from math import log
import numpy as np
import os
import pandas as pd
from pathlib import Path
import re
from sklearn.model_selection import train_test_split
import sys

from lightfm import LightFM
from lightfm.data import Dataset
from lightfm.evaluation import auc_score


def _get_items(data_home, data_file=None):
    """
    Load items data, then apply (naive) NLP to reduce text content to relatively meaningful bag-of-words.
    Bag-of-Words is appended to the item features matrix.

    :param data_home:
    :param data_file:
    :return:  dataframe of items with text content replaced by tf-idf matrix of terms.
    """
    # Load items data.
    file_path = os.path.join(os.path.abspath(data_home), data_file)
    items = pd.read_csv(file_path, sep=',', encoding='utf-8')

    # No NLP for now.
    nlp_active = False
    if nlp_active == True:
        # Apply NLP processing to text documents.
        bows = _nlp_naive(items['document'])

        # Drop the text content column and replace with BOW matrix.
        items.drop(['document'], axis=1, inplace=True)
        items = items.join(bows)
    else:
        items.drop(['document'], axis=1, inplace=True)

    return items


def _nlp_naive(docs):
    """
    Naive implementation of NLP, i.e. a language agnostic approach to Bag-of-Words.

    Includes:
        Normalisation
        Tokenisation
        Stopwords
        TF-IDF

    Excludes in-depth grammatical and entity analysis, e.g:
        UPOS tagging
        Named entity recognition
        Lemmatisation
        Stemming

    :param docs: Text documents
    :return: array of {term : count} dicts
    """

    # Retrieve stopwords lists.
    scriptpath, scriptname = os.path.split(os.path.abspath(__file__))
    with open(scriptpath + '/totara/stopwords-iso/stopwords-iso.json') as json_file:
        stopwords_lists = json.load(json_file)

        # Normalise input documents as terms (tokens).
    normalised = _normalise_doc_text(docs, stopwords_lists)

    # Calculate term frequencies.
    term_frequencies = []
    for term_sets in normalised:
        term_frequencies.append(_term_frequencies(term_sets))

    # Inverse document frequencies.
    idfs = _inverse_document_frequencies(term_frequencies)

    return idfs


def _normalise_doc_text(docs, stopwords_lists):
    """
    Normalise text to word collections for further processing.

    :param docs:
    :param stopwords_lists:
    :return:
    """
    normalised_docs = []
    for doc in docs:
        # Detect most likely language before transforming content.
        lang = langid.classify(doc)

        # All text to lowercase.
        doc = doc.lower()

        # Replace punctuation with space.
        doc = re.sub(r'[^\w\s]', ' ', doc)

        # Replace (multiple) whitespace with single printable space.
        doc = ' '.join(doc.split())

        # Tokenise on space.
        tokenized = re.split(r'\s', doc)
        counted_words = Counter(tokenized)
        cleaned_wordlist = dict(counted_words)

        # Get stopword list.
        stopwords = stopwords_lists.get(lang[0])

        # Remove stopwords (if we found a list to process).
        if stopwords is None:
            stopwords = []

        if len(stopwords) > 0:
            for thisword in counted_words.keys():
                if thisword in stopwords:
                    del cleaned_wordlist[thisword]

            try:
                del cleaned_wordlist['']
            except:
                pass
        normalised_docs.append(cleaned_wordlist)

    return normalised_docs


def _term_frequencies(document):
    """
    Take a list of counted words and return a dict of term frequencies.

    :param document:
    :return:
    """
    number_of_words = len(document)
    term_frequencies = {term: count / number_of_words for (term, count) in document.items()}

    return term_frequencies


def _inverse_document_frequencies(term_frequencies):
    """
    Take a list of term frequency dicts and return a dict of inverse document frequencies.

    :param term_frequencies:
    :return:
    """
    terms = set([i for ls in map(lambda x: x.keys(), term_frequencies) for i in ls])
    n = len(term_frequencies)
    counts = {}
    for term in terms:
        count = 0
        for d in term_frequencies:
            if term in d:
                count = count + 1
        counts[term] = count
    inverse_document_frequencies = {t: log(n / counts[t]) for t in terms}

    tf_idfs = [{t: tf * inverse_document_frequencies[t] for (t, tf) in d.items()} for d in term_frequencies]
    tf_idfs_df = pd.DataFrame(tf_idfs)
    tf_idfs_df = tf_idfs_df.fillna(0)

    return tf_idfs_df


# ------------------------------------------------------------------------------

def _get_users(data_home, data_file=None):
    """
    Load user data and one-hot encode language (categorical data).

    :param data_home:
    :param data_file:
    :return:  dataframe of users.
    """
    # Load items data.
    file_path = os.path.join(os.path.abspath(data_home), data_file)
    users = pd.read_csv(file_path, sep=',', encoding='utf-8')
    users = pd.concat([users.drop('lang', axis=1), pd.get_dummies(users['lang'])], axis=1)

    return users


# ------------------------------------------------------------------------------

def _get_interactions(data_home, data_file=None, num_users=0, num_items=0, training_size=0.8):
    """
    Load interactions data and build interactions matrix for model.

    :param data_home:
    :param data_file:
    :param num_users:
    :param num_items:
    :return:
    """
    # Read raw data.
    file_path = os.path.join(os.path.abspath(data_home), data_file)
    interactions = pd.read_csv(file_path, sep=',', encoding='utf-8')

    # In matrix-factorisation mode these values would not be known yet.
    if num_users == None:
        num_users = len(interactions.user_id.unique())
        num_items = len(interactions.item_id.unique())

    # Split into training and test sets.
    train, test = train_test_split(interactions, train_size=training_size, random_state=42, shuffle=True)

    return train, test, num_users, num_items


def _make_item_features(item_features=None, item_feature_labels=None):
    feature_variant_names = []
    feature_unique_values = []
    for label in item_feature_labels:
        feature_variant_names.extend([label] * len(item_features[label].unique()))
        feature_unique_values.extend(list(item_features[label].unique()))
    item_feature_possible_values = _feature_list(feature_variant_names, feature_unique_values)

    return item_feature_possible_values

# ------------------------------------------------------------------------------

def _sample_hyperparameters():
    while True:
        yield {
            "no_components": np.random.randint(16, 64),
            "learning_schedule": np.random.choice(["adagrad", "adadelta"]),
            "loss": np.random.choice(["bpr", "warp", "warp-kos"]),
            "learning_rate": np.random.exponential(0.05),
            "item_alpha": np.random.exponential(1e-8),
            "user_alpha": np.random.exponential(1e-8),
            "max_sampled": np.random.randint(5, 15),
            "num_epochs": np.random.randint(5, 50),
        }


def _random_search(train, test, num_samples=10, num_threads=1):
    for hyperparams in itertools.islice(_sample_hyperparameters(), num_samples):
        num_epochs = hyperparams.pop("num_epochs")

        model = LightFM(**hyperparams)
        model.fit(train, epochs=num_epochs, num_threads=num_threads)

        score = auc_score(model, test, train_interactions=train, num_threads=num_threads,
                          check_intersections=False).mean()

        hyperparams["num_epochs"] = num_epochs

        yield (score, hyperparams, model)


# ------------------------------------------------------------------------------

def _similar_items(internal_idx, model, num_results=None, num_items=None):
    item_representations = model.get_item_representations()[1][0:num_items]

    # Cosine similarity
    scores = item_representations.dot(item_representations[internal_idx, :])
    item_norms = np.linalg.norm(item_representations, axis=1)
    scores /= item_norms

    best = np.argpartition(scores, -num_results)[-num_results:]
    similars = sorted(zip(best, scores[best] / item_norms[internal_idx]),
                  key=lambda x: -x[1])

    # The most similar item is the item itself, let's drop that.
    similars.pop(0)

    return similars


# ------------------------------------------------------------------------------

def _feature_list(feature_labels, feature_values):
    """
    Create list 'feature:value' pairs.

    :param feature_labels:
    :param feature_values:
    :return:
    """
    features = []
    for label, value in zip(feature_labels, feature_values):
        feature = str(label) + ":" + str(value)
        features.append(feature)
    return features

# ------------------------------------------------------------------------------

"""
TODO: (... or not to do... it will throw errors if incompatibilities exist anyway...)
# Confirm suitable version of Python.
sys_version = sys.version_info
if not (sys_version.major == 3 and sys_version.minor > 5):
    raise Exception("Python 3.6 or higher required")
"""

# Define expected arguments.
parser = argparse.ArgumentParser(description="Totara Engage recommendations")
parser.add_argument('--query',
                    help='The type of query to run (mf = matrix factorisation, hybrid = matrix factorisation & content-filtering',
                    required=True, choices=['mf', 'hybrid'])
parser.add_argument("--result_count_user", help="Number of items-to-user recommendations to return", required=True,
                    type=int)
parser.add_argument("--result_count_item", help="Number of items-to-item recommendations to return", required=True,
                    type=int)
parser.add_argument("--threads", help="Number of parallel threads to use (should be <= physical cores)", required=True,
                    type=int)
parser.add_argument('--data_path', help='Path to data directory', required=True, type=Path)
args = parser.parse_args()

# Set runtime variables from arguments.
query = args.query
data_home = args.data_path
num_threads = args.threads
user_result_count = args.result_count_user
item_result_count = args.result_count_item

# There is no immediate reason why the standard 80%/20% split for training and test data needs to be changed.
training_size = 0.8

# Initialise working variables.
users = None
num_users = None
user_features = None
users_feature_names = None

items = None
num_items = None
item_features = None
item_feature_names = None

# Load user/item interaction data into training and test sets.
raw_interactions_train, raw_interactions_test, num_users, num_items = \
    _get_interactions(data_home, 'user_interactions.csv', num_users, num_items, training_size)

# Load user and item data for hybrid algorithm.
if query == 'hybrid':
    # Load and pre-process user data.
    users_raw = _get_users(data_home, 'user_data.csv')
    num_users = users_raw.shape[0]
    unique_users = users_raw['user_id']
    users_raw = None

    # Load and pre-process item data.
    items_raw = _get_items(data_home, 'item_data.csv')
    num_items = items_raw.shape[0]
    unique_items = items_raw['item_id']
    item_feature_names = list(items_raw.columns.values)
    item_feature_names.pop(0)
    item_feature_possible_values = _make_item_features(items_raw, item_feature_names)
else:
    all_interactions = pd.concat([raw_interactions_train, raw_interactions_test])
    unique_items = all_interactions['item_id'].unique()
    unique_users = all_interactions['user_id'].unique()
    all_interactions = None

# ------------------------------------------------------------------------------

# Fit the training and test datasets, then build their respective interaction matrices.
if query == 'hybrid':
    dataset = Dataset()
    dataset.fit(unique_users, unique_items, item_features=item_feature_possible_values)
else:
    dataset = Dataset()
    dataset.fit(unique_users, unique_items)

# Fit interactions.
(train_interactions, train_weights) = dataset.build_interactions(
    [(x[0], x[1], x[2]) for x in raw_interactions_train.values])
(test_interactions, test_weights) = dataset.build_interactions(
    [(x[0], x[1], x[2]) for x in raw_interactions_test.values])

# ------------------------------------------------------------------------------

# Add in the features.
if query == 'hybrid':
    # Prep user features (excluding item_id).
    features_only = items_raw[item_feature_names]
    features_list = [list(value) for value in features_only.values]
    feature_list = []
    for feature_row in features_list:
        feature_list.append(_feature_list(item_feature_names, feature_row))

    # Form user tuples and build user features.
    item_tuples = list(zip(items_raw.item_id, feature_list))
    item_features = dataset.build_item_features(item_tuples, normalize=False)

# ------------------------------------------------------------------------------

# Model hyperparameter optimisation.
(score, hyperparams, model) = max(_random_search(train_interactions, test_interactions, num_threads=num_threads),
                                  key=lambda x: x[0])

# Fit model on all interactions using calculated optimum parameters.
model = LightFM(
    no_components=hyperparams['no_components'],
    learning_schedule=hyperparams['learning_schedule'],
    loss=hyperparams['loss'],
    learning_rate=hyperparams['learning_rate'],
    item_alpha=hyperparams['item_alpha'],
    user_alpha=hyperparams['user_alpha'],
    max_sampled=hyperparams['max_sampled']
)

if query == 'hybrid':
    model.fit(train_interactions, user_features=user_features, item_features=item_features, epochs=hyperparams['num_epochs'],
              num_threads=num_threads)
    model.fit_partial(test_interactions, user_features=user_features, item_features=item_features, epochs=hyperparams['num_epochs'],
                      num_threads=num_threads)
else:
    model.fit(train_interactions, epochs=hyperparams['num_epochs'], num_threads=num_threads)
    model.fit_partial(test_interactions, epochs=hyperparams['num_epochs'], num_threads=num_threads)

# ------------------------------------------------------------------------------

# Get id maps.
user_id_map, user_feature_map, item_id_map, item_feature_map = dataset.mapping()

# Prepare user and item id lookup tables.
lookup_uid = {lfm_id: totara_id for totara_id, lfm_id in user_id_map.items()}
lookup_iid = {lfm_id: totara_id for totara_id, lfm_id in item_id_map.items()}

# ------------------------------------------------------------------------------

# Items to item (I2I) recommendations.
i2i = pd.DataFrame({'target_iid': [], 'similar_iid': [], 'totara_target_iid': [], 'totara_similar_iid': [], 'ranking': []})
i2i.totara_target_iid = i2i.totara_target_iid.astype(str)
i2i.totara_similar_iid = i2i.totara_similar_iid.astype(str)

item_result_count += 1
for totara_iid, lfm_iid in item_id_map.items():

    similars = _similar_items(lfm_iid, model, num_results=item_result_count, num_items=num_items)
    predictions = pd.DataFrame(similars, columns=['similar_iid', 'ranking'])
    predictions['target_iid'] = lfm_iid
    predictions['totara_target_iid'] = lookup_iid[lfm_iid]

    for i, row in predictions.iterrows():
        totara_similar_iid = lookup_iid[row['similar_iid']]
        predictions.at[i, 'totara_similar_iid'] = totara_similar_iid
    i2i = pd.concat([i2i, predictions])

i2i = i2i.drop(['similar_iid'], axis=1)
i2i = i2i.drop(['target_iid'], axis=1)
file_path = os.path.join(os.path.abspath(data_home), 'i2i.csv')
with open(file_path, 'w', newline='') as csv_out:
    i2i.to_csv(csv_out, columns=['totara_target_iid', 'totara_similar_iid', 'ranking'], header=['target_iid', 'similar_iid', 'ranking'],
               index=False, encoding='utf-8', float_format='%.12f', mode='w')
    csv_out.close()
i2i = None

# ------------------------------------------------------------------------------

# Items to user (I2U) recommendations.
i2u = pd.DataFrame({'uid': [], 'iid': [], 'totara_iid': [], 'ranking': []})
i2u.totara_iid = i2u.totara_iid.astype(str)

for totara_uid, lfm_uid in user_id_map.items():
    predictions = pd.DataFrame({'uid': [], 'iid': [], 'totara_iid': [], 'ranking': []})
    predictions['iid'] = np.arange(num_items)
    predictions['uid'] = totara_uid
    predictions.totara_iid = predictions.totara_iid.astype(str)
    predictions['ranking'] = model.predict(lfm_uid, np.arange(num_items), num_threads=num_threads)
    predictions = predictions.sort_values(by=['ranking', 'iid'],
                                          ignore_index=True, ascending=False).iloc[:user_result_count, :]

    for i, row in predictions.iterrows():
        totara_iid = lookup_iid[row['iid']]
        predictions.at[i, 'totara_iid'] = totara_iid
    i2u = pd.concat([i2u, predictions])

# Write to csv.
i2u = i2u.drop(['iid'], axis=1)
file_path = os.path.join(os.path.abspath(data_home), 'i2u.csv')
with open(file_path, 'w', newline='') as csv_out:
    i2u.to_csv(csv_out, columns=['uid', 'totara_iid', 'ranking'], header=['uid', 'iid', 'ranking'],
               index=False, encoding='utf-8', float_format='%.12f', mode='w')
    csv_out.close()
i2u = None
