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

@author Amjad Ali <amjad.ali@totaralearning.com>
@package ml_recommender
"""

import os
import json
import langid
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from lightfm.data import Dataset
from .pre_processors import PreProcessors


class DataLoader:
    """
    This is a conceptual representation of the process to read, preprocess and transform data that
    was exported by the Totara instance, so that the data is consumable by the LightFM model class.
    """
    def __init__(self, data_home=None, nl_libs=None, query='mf', tenant=0):
        """
        Class constructor method
        :param data_home: Full path of the directory that contains the data exported by the Totara instance,
            defaults to None
        :type data_home: str, mandatory
        :param nl_libs: Full path to the directory containing the language processing resources e.g.,
            stopwords of different languages, lemmatizing resources, etc., defaults to None
        :type nl_libs: str, mandatory
        :param query: One of 'mf' (collaborative filtering), 'partial' (content based filtering
            without text processing), or 'hybrid' (content based filtering with text processing). The data
            preparation/processing depends on this parameter, defaults to 'mf'
        :type query: str, optional
        :param tenant: The tenant id whose data needs to be processed, defaults to '0'
        :type tenant: str, optional
        """
        self.data_home = data_home
        self.nl_libs = nl_libs
        self.query = query
        self.tenant = tenant

    def __get_interactions(self):
        """
        This method reads the given tenant's `user_interaction` file and returns the interactions dataset.
        :return: A list of tuples (user_id, item_id, weight) containing user-item interactions
        :rtype: list
        """
        file_path = os.path.join(self.data_home, f'user_interactions_{self.tenant}.csv')
        interactions = pd.read_csv(filepath_or_buffer=file_path, sep=',', encoding='utf-8')
        interactions = [(int(x[0]), x[1], float(x[2])) for x in interactions.to_numpy()]
        return interactions

    def __get_users(self):
        """
        Reads the file `user_data` of the given tenant and returns a list of user ids
        :return: A list of user ids
        """
        file_path = os.path.join(self.data_home, f'user_data_{self.tenant}.csv')
        users_data = pd.read_csv(filepath_or_buffer=file_path, sep=',', encoding='utf-8', index_col='user_id')
        user_ids = users_data.index.tolist()
        return user_ids

    @staticmethod
    def __create_feature_dict(row):
        """
        Converts a pandas series into a dictionary whose keys are series' indices and the values are series' values.
        Removes entries where the values are not 1
        :param row: One record from the pandas DataFrame of items features
        :type row: Pandas Series object
        :return: A dictionary whose keys are series' indices and the values are series' values
        """
        nonzero_row = row[row == 1]
        features = dict(zip(nonzero_row.index.tolist(), nonzero_row.tolist()))
        return features

    def __create_partial_features(self, dataframe=None):
        """
        This method prepares a list of tags `(item_id, [tag1, tag2, tag3, ...])`; by adding only those tags to
        each item where that tag (column header) had a value 1 in the pandas DataFrame
        :param dataframe: A pandas DataFrame where row labels are item ids and column headers are tags. The values
            of tags for each item can be 0 or 1, defaults to None
        :type dataframe: pandas DataFrame, mandatory
        :return: list of tuples where each tuple if of the shape `(item_id, [tag1, tag2, tag3, ...])`
        :rtype: list
        """
        dataframe['features'] = dataframe.apply(lambda row: self.__create_feature_dict(row), axis=1)
        features_zip = zip(dataframe.index.tolist(), dataframe.features.tolist())
        return list(features_zip)

    @staticmethod
    def __get_items_attr(dataframe=None):
        """
        This method creates a map between the item_id and item_type from the input dataframe
        :param dataframe: A pandas DataFrame where row labels are item ids, column headers are item_types and the
            values in these columns are binary coded (0 or 1), defaults to None
        :type dataframe: Pandas DataFrame object
        :return: A dictionary whose keys are item_id and the values are item_type
        """
        dataframe_stacked = dataframe[dataframe==1].stack().reset_index()
        item_type_map = pd.Series(dataframe_stacked.level_1.values, index=dataframe_stacked.item_id).to_dict()
        return item_type_map

    def __get_items(self):
        """
        This method reads the data from the `item_data` file of the given tenant and returns a fully processed
        items data. The processing depends on the type of `query` defined in the instance variable of the class
        :return: A dictionary containing four items; 'features_list' - a full list of all the possible features
            of the items data, 'items_features_data' - A list containing tuples of the shape
            `(item_id, {features_name: weight, ...})`, `item_ids` - a list of item ids and `item_type_map` - a
            dictionary with keys as the `item_id` and values as `item_type`
        """
        file_path = os.path.join(self.data_home, f'item_data_{self.tenant}.csv')
        items_data = pd.read_csv(filepath_or_buffer=file_path, sep=',', encoding='utf-8', index_col='item_id')
        item_ids = items_data.index.tolist()
        type_cols = ['container_course', 'container_workspace', 'engage_article', 'engage_microlearning', 'totara_playlist']

        item_type_map = self.__get_items_attr(dataframe=items_data[type_cols])

        if self.query == 'hybrid':
            # Retrieve stopwords list.
            stopwords_file = os.path.join(self.nl_libs, 'stopwords-iso.json')
            with open(stopwords_file) as json_file:
                stopwords_list = json.load(json_file)
            processed_document = []
            for doc in items_data.document:
                # Predict language of the document
                lang = langid.classify(doc)
                # Pick stopwords list of the predicted language
                stopwords = stopwords_list.get(lang[0])
                # Cleanup the document and remove stopwords from it using the Preprocessors class
                new_doc = PreProcessors(
                    raw_doc=doc
                ).preprocess_docs(stopwords=stopwords)
                processed_document.append(new_doc)

            # Convert the list of documents into a matrix of TF-IDF features
            tf = TfidfVectorizer()
            tf.fit(processed_document)
            transformed_doc = tf.transform(processed_document)

            # --------------------------------------------------------------
            # Convert the matrix of TF-IDF features into a list where each row of the matrix
            # is transformed into an element of the list. This element is a dictionary where
            # the keys are the feature names (words from the cleaned document) and each value
            # is TF-IDF value of that word.
            features_list = tf.get_feature_names()
            text_features = []
            for item in range(transformed_doc.shape[0]):
                indices = transformed_doc[item, :].indices
                features = {}
                if len(indices) > 0:
                    for index in indices:
                        features[features_list[index]] = transformed_doc[item, index]
                text_features.append(features)
            # -----------------------------------------------------------------
            # Create a list of features from the rest of the headers of the `items_data` DataFrame
            # where each element of this list is a tuple of the shape
            # (item_id, {feature_1: weight1, feature2: weight2, ...}), where weight1, weight2, etc are
            # all 1's, i.e., only the features/tags that have values as 1 will appear in this dictionary
            # for all items.
            partial_features = self.__create_partial_features(items_data.drop(columns=['document']))
            # -------------------------------------------------------------------
            # Append the two sets of features (text features and the other features/tags) together so
            # that the resultant shape of the list stays as mentioned for the list `partial_features`
            items_features_data = [
                (item[0][0], {**item[0][1], **item[1]}) for item in zip(partial_features, text_features)
            ]
            # ---------------------------------------------------------------------
            # Create a list of all the feature names (tags and words)
            features_list = items_data.columns.tolist() + features_list
            features_list.remove('document')
        elif self.query == 'partial':
            # As for the case of `query == 'hybrid'` except there are no text features
            items_features_data = self.__create_partial_features(items_data.drop(columns=['document']))
            # List of features names (tags)
            features_list = items_data.columns.tolist()
            features_list.remove('document')
        else:
            # No item features for `query == 'mf'` or pure collaborative filtering
            items_features_data = None
            features_list = None

        items_processed_data = {
            'items_features_data': items_features_data,
            'features_list': features_list,
            'item_ids': item_ids,
            'item_type_map': item_type_map
        }

        return items_processed_data

    def __transform_data(self):
        """
        This method governs the process of reading the interactions data, items data and the users data
        as defined in the class instance variables
        :return: A dictionary of three items; 'interaction' - the interactions data, 'items_data' - the
            items data, and the 'user_ids' - the user data.
        """
        print(f'Processing tenant {self.tenant}')

        try:
            interactions = self.__get_interactions()
        except ValueError as err:
            interactions = None
            print(f'ValueError: {err}')
            print(f'Cannot process tenant {self.tenant}. Perhaps not enough data yet.')
            exit()

        items_data = self.__get_items()
        user_ids = self.__get_users()

        processed_data = {
            'interactions': interactions,
            'items_data': items_data,
            'user_ids': user_ids
        }
        return processed_data

    def load_data(self):
        """
        This method takes reads runs other methods of the class to read and preprocess interactions, items and users
        data and transforms that into the sparse matrices that can be consumed by the LightFM model class.
        :return: A dictionary with the items; `interactions` - a sparse matrix of user-item interaction, `weights` - a
            sparse matrix of of sample weights of the same shape as the `interactions`, `item_features` - a sparse
            matrix of the shape `[n_items, n_features]` where each row contains item's weights over features,
            `mapping` - a tuple of four dictionaries (user id map, user features map, item id map, item feature map),
            and `item_type_map` - a dictionay with keys as the `item_id` and values as `item_type`
        """
        # Read all datasets, preprocess and transform data to be consumed by the LightFM data class
        transformed_data = self.__transform_data()
        # Instantiate Dataset class
        dataset = Dataset(user_identity_features=False, item_identity_features=False)

        # Use fit method of the Dataset class to setup the user/item id and feature name mappings.
        dataset.fit(
            users=transformed_data['user_ids'],
            items=transformed_data['items_data']['item_ids'],
            item_features=transformed_data['items_data']['features_list']
        )

        # Prepare the interaction and weights sparse matrices
        interactions, weights = dataset.build_interactions(data=transformed_data['interactions'])

        if self.query in ['partial', 'hybrid']:
            # Prepare the item features sparse matrix if the user is not asking for content based filtering
            item_features = dataset.build_item_features(
                data=transformed_data['items_data']['items_features_data']
            )
        else:
            # No item features matrix if the user wants only the collaborative filtering
            item_features = None

        results = {
            'interactions': interactions,
            'weights': weights,
            'item_features': item_features,
            'mapping': dataset.mapping(),
            'item_type_map': transformed_data['items_data']['item_type_map']
        }
        return results
