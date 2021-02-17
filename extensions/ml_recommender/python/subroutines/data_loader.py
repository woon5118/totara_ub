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

import json
import langid
import os
import pandas as pd
from lightfm.data import Dataset
from sklearn.feature_extraction.text import TfidfVectorizer

from subroutines.pre_processors import PreProcessors


class DataLoader:
    """
    This is a conceptual representation of the process to read, preprocess and transform
    data that was exported by the Totara instance, so that the data is consumable by the
    LightFM model class.
    """

    def __init__(self, nl_libs=None):
        """
        Class constructor method
        :param nl_libs: Full path to the directory containing the language processing
            resources e.g., stopwords of different languages, lemmatizing resources,
            etc.
        :type nl_libs: str
        """
        self.nl_libs = nl_libs

    @staticmethod
    def __get_interactions(interactions_df=None):
        """
        This method uses the `interactions_df` DataFrame and returns a tuple object
            composed of interactions (a list) and positive_inter_map (a dictionary with
            user-to-items information).
        :param interactions_df: The interactions data as exported from the Totara
            instance
        :type interactions_df: An instance of pandas DataFrame
        :return: A tuple where the first element is a list of tuples (user_id, item_id,
            weight) containing user-item interactions, and the second element is a
            dictionary whose keys are the Totara user ids and values are the lists
            containing the Totara item ids that user has interacted with in the past.
            Note that this dictionary only contains the Totara user ids of those users
            who have had at one interaction with an item in the past
        :rtype: tuple
        """
        positive_interactions = interactions_df[interactions_df.rating == 1]
        users_interacted = positive_interactions.user_id.unique()
        positive_inter_map = dict(
            (
                u,
                positive_interactions[
                    positive_interactions.user_id == u
                ].item_id.tolist(),
            )
            for u in users_interacted
        )
        interactions = [
            (int(x[0]), x[1], float(x[2])) for x in interactions_df.to_numpy()
        ]
        return interactions, positive_inter_map

    @staticmethod
    def __get_users(users_data=None):
        """
        Uses the pandas DataFrame `users_data` and returns a list of user ids
        :param users_data: The users data exported from the Totara instance
        :type users_data: A pandas DataFrame
        :return: A list of user ids
        """
        user_ids = users_data.index.tolist()
        return user_ids

    @staticmethod
    def __create_feature_dict(row):
        """
        Converts a pandas series into a dictionary whose keys are series' indices and
        the values are series' values. Removes entries where the values are not 1
        :param row: One record from the pandas DataFrame of items features
        :type row: Pandas Series object
        :return: A dictionary whose keys are series' indices and the values are series'
            values
        """
        nonzero_row = row[row == 1]
        features = dict(zip(nonzero_row.index.tolist(), nonzero_row.tolist()))
        return features

    def __create_partial_features(self, dataframe=None):
        """
        This method prepares a list of tags `(item_id, [tag1, tag2, tag3, ...])`; by
        adding only those tags to each item where that tag (column header) had a value 1
        in the pandas DataFrame
        :param dataframe: A pandas DataFrame where row labels are item ids and column
            headers are tags. The values of tags for each item can be 0 or 1
        :type dataframe: pandas DataFrame
        :return: list of tuples where each tuple if of the shape `(item_id, [tag1, tag2,
            tag3, ...])`
        :rtype: list
        """
        dataframe["features"] = dataframe.apply(
            lambda row: self.__create_feature_dict(row), axis=1
        )
        features_zip = zip(dataframe.index.tolist(), dataframe.features.tolist())
        return list(features_zip)

    @staticmethod
    def __get_items_attr(dataframe=None):
        """
        This method creates a map between the item_id and item_type from the input
        dataframe
        :param dataframe: A pandas DataFrame where row labels are item ids, column
            headers are item_types and the values in these columns are binary coded
            (0 or 1)
        :type dataframe: Pandas DataFrame object
        :return: A dictionary whose keys are item_id and the values are item_type
        """
        dataframe_stacked = dataframe[dataframe == 1].stack().reset_index()
        item_type_map = pd.Series(
            dataframe_stacked.level_1.values, index=dataframe_stacked.item_id
        ).to_dict()
        return item_type_map

    def __get_items(self, items_data=None, query="mf"):
        """
        This method uses the pandas DataFrame of the items data exported from the totara
        instance and returns a fully processed items data. The processing depends on the
        type of `query` defined in the instance variable of the class, defaults to None
        :param items_data: The data exported from the Totara instance
        :type items_data: A pandas DataFrame
        :param query: One of 'mf' (collaborative filtering), 'partial' (content based
            filtering without text processing), or 'hybrid' (content based filtering
            with text processing). The data preparation/processing depends on this
            parameter, defaults to 'mf'
        :type query: str, optional
        :return: A dictionary containing four items; 'features_list' - a full list of
            all the possible features of the items data, 'items_features_data' - A list
            containing tuples of the shape `(item_id, {features_name: weight, ...})`,
            `item_ids` - a list of item ids and `item_type_map` - a dictionary with keys
            as the `item_id` and values as `item_type`
        """

        item_ids = items_data.index.tolist()
        type_cols = [
            "container_course",
            "container_workspace",
            "engage_article",
            "engage_microlearning",
            "totara_playlist",
        ]

        item_type_map = self.__get_items_attr(dataframe=items_data[type_cols])

        if query == "hybrid":
            # Retrieve stopwords list.
            stopwords_file = os.path.join(self.nl_libs, "stopwords-iso.json")
            with open(stopwords_file) as json_file:
                stopwords_list = json.load(json_file)
            processed_document = []
            for doc in items_data.document:
                # Predict language of the document
                lang = langid.classify(doc)
                # Pick stopwords list of the predicted language
                stopwords = stopwords_list.get(lang[0])
                # Cleanup the document and remove stopwords from it using the
                # Preprocessors class
                new_doc = PreProcessors(stopwords=stopwords).preprocess_docs(
                    raw_doc=doc
                )
                processed_document.append(new_doc)

            # Convert the list of documents into a matrix of TF-IDF features
            tf = TfidfVectorizer()
            tf.fit(processed_document)
            transformed_doc = tf.transform(processed_document)

            # --------------------------------------------------------------
            # Convert the matrix of TF-IDF features into a list where each row of the
            # matrix is transformed into an element of the list. This element is a
            # dictionary where the keys are the feature names (words from the cleaned
            # document) and each value is TF-IDF value of that word.
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
            # Create a list of features from the rest of the headers of the `items_data`
            # DataFrame where each element of this list is a tuple of the shape
            # (item_id, {feature_1: weight1, feature2: weight2, ...}), where weight1,
            # weight2, etc are all 1's, i.e., only the features/tags that have values as
            # 1 will appear in this dictionary for all items.
            partial_features = self.__create_partial_features(
                items_data.drop(columns=["document"])
            )
            # -------------------------------------------------------------------
            # Append the two sets of features (text features and the other
            # features/tags) together so that the resultant shape of the list stays as
            # mentioned for the list `partial_features`
            items_features_data = [
                (item[0][0], {**item[0][1], **item[1]})
                for item in zip(partial_features, text_features)
            ]
            # ---------------------------------------------------------------------
            # Create a list of all the feature names (tags and words)
            features_list = items_data.columns.tolist() + features_list
            features_list.remove("document")
        elif query == "partial":
            # As for the case of `query == 'hybrid'` except there are no text features
            items_features_data = self.__create_partial_features(
                items_data.drop(columns=["document"])
            )
            # List of features names (tags)
            features_list = items_data.columns.tolist()
            features_list.remove("document")
        else:
            # No item features for `query == 'mf'` or pure collaborative filtering
            items_features_data = None
            features_list = None

        items_processed_data = {
            "items_features_data": items_features_data,
            "features_list": features_list,
            "item_ids": item_ids,
            "item_type_map": item_type_map,
        }

        return items_processed_data

    def __transform_data(
        self, interactions_df=None, items_data=None, users_data=None, query="mf"
    ):
        """
        This method governs the process of reading the interactions data, items data and
        the users data as defined in the class instance variables
        :param interactions_df: The interactions data as exported from the Totara
            instance
        :type interactions_df: A pandas DataFrame
        :param items_data: The data exported from the Totara instance
        :type items_data: A pandas DataFrame
        :param users_data: The users data exported from the Totara instance
        :type users_data: A pandas DataFrame
        :param query: One of 'mf' (collaborative filtering), 'partial' (content based
            filtering without text processing), or 'hybrid' (content based filtering
            with text processing). The data preparation/processing depends on this
            parameter, defaults to 'mf'
        :type query: str, optional
        :return: A dictionary of four items; 'interaction' - the interactions data,
            `positive_inter_map` - the users-to-items_list map (where positive
            interactions happened), 'items_data' - the items data, and the 'user_ids' -
            the user data.
        """

        interactions, positive_inter_map = self.__get_interactions(
            interactions_df=interactions_df
        )
        items_data = self.__get_items(items_data=items_data, query=query)
        user_ids = self.__get_users(users_data=users_data)

        processed_data = {
            "interactions": interactions,
            "positive_inter_map": positive_inter_map,
            "items_data": items_data,
            "user_ids": user_ids,
        }
        return processed_data

    def load_data(
        self, interactions_df=None, items_data=None, users_data=None, query="mf"
    ):
        """
        This method takes reads runs other methods of the class to read and preprocess
        interactions, items and users data and transforms that into the sparse matrices
        that can be consumed by the LightFM model class.
        :param interactions_df: The interactions data as exported from the Totara
            instance
        :type interactions_df: A pandas DataFrame
        :param items_data: The data exported from the Totara instance
        :type items_data: A pandas DataFrame
        :param users_data: The users data exported from the Totara instance
        :type users_data: A pandas DataFrame
        :param query: One of 'mf' (collaborative filtering), 'partial' (content based
            filtering without text processing), or 'hybrid' (content based filtering
            with text processing). The data preparation/processing depends on this
            parameter, defaults to 'mf'
        :type query: str, optional
        :return: A dictionary with the items; `interactions` - a sparse matrix of
            user-item interaction, `weights` - a sparse matrix of of sample weights of
            the same shape as the `interactions`, `item_features` - a sparse matrix of
            the shape `[n_items, n_features]` where each row contains item's weights
            over features, `mapping` - a tuple of four dictionaries (user id map, user
            features map, item id map, item feature map), `item_type_map` - a dictionary
            with keys as the `item_id` and values as `item_type`, and
            `positive_inter_map` - a dictionary where keys are the Totara user ids (of
            the users who interacted with at least one item) and values are lists of the
            Totara item ids
        """
        # Read all datasets, preprocess and transform data to be consumed by the LightFM
        # data class
        transformed_data = self.__transform_data(
            interactions_df=interactions_df,
            items_data=items_data,
            users_data=users_data,
            query=query,
        )
        # Instantiate Dataset class
        dataset = Dataset(user_identity_features=False, item_identity_features=False)

        # Use fit method of the Dataset class to setup the user/item id and feature
        # name mappings
        dataset.fit(
            users=transformed_data["user_ids"],
            items=transformed_data["items_data"]["item_ids"],
            item_features=transformed_data["items_data"]["features_list"],
        )

        # Prepare the interaction and weights sparse matrices
        interactions, weights = dataset.build_interactions(
            data=transformed_data["interactions"]
        )

        if query in ["partial", "hybrid"]:
            # Prepare the item features sparse matrix if the user is not asking for
            # content based filtering
            item_features = dataset.build_item_features(
                data=transformed_data["items_data"]["items_features_data"]
            )
        else:
            # No item features matrix if the user wants only the collaborative filtering
            item_features = None

        results = {
            "interactions": interactions,
            "weights": weights,
            "item_features": item_features,
            "mapping": dataset.mapping(),
            "item_type_map": transformed_data["items_data"]["item_type_map"],
            "positive_inter_map": transformed_data["positive_inter_map"],
        }
        return results
