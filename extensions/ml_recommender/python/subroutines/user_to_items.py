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

import numpy as np
import pandas as pd


class UserToItems:
    """
    This is a conceptual representation for generating the item recommendations for users
    """
    def __init__(
            self,
            u_mapping=None,
            i_mapping=None,
            item_type_map=None,
            item_features=None,
            model=None,
            num_items=10,
            num_threads=2
    ):
        """
        Constructor method
        :param u_mapping: A dictionary where keys are Totara user ids and values are internal user ids
        :type u_mapping: dict
        :param i_mapping: A dictionary where keys are Totara item ids and values are internal item ids
        :type i_mapping: dict
        :param item_type_map: A dictionary where keys are Totara item ids and values are item types, e.g.,
            one of `container_course`, `container_workspace`, `engage_article`, `engage_microlearning`,
            and `totara_playlist`
        :type item_type_map: dict
        :param item_features: A sparse matrix of shape `[n_items, n_item_features]` - Each row contains
            that item's weight over features
        :type item_features: csr_matrix
        :param model: The model to be evaluated
        :type model: LightFM model instance
        :param num_items: Number of top ranked items user wants to be recommended
        :type num_items: int
        :param num_threads: Number of parallel computation threads to use
        :type num_items: int
        """
        self.u_mapping = u_mapping
        self.i_mapping = i_mapping
        self.i_mapping_rev = {v: k for k, v in i_mapping.items()}
        self.item_type_map = item_type_map
        self.item_features = item_features
        self.model = model
        self.num_items = num_items
        self.num_threads = num_threads

    def __get_items(self, internal_uid):
        """
        Returns top `num_items` recommended items where `num_items` is the instance variable of the class
        :param internal_uid: The internal id of the user for whom the recommendations are sought
        :type internal_uid: int
        :return: A list of tuples where the first elements are the Totara ids of the items and the second
            ones the ranking
        :rtype: list
        """
        item_ids = np.fromiter(self.i_mapping.values(), dtype=np.int32)
        predictions = self.model.predict(
            user_ids=internal_uid,
            item_ids=item_ids,
            user_features=None,
            item_features=self.item_features,
            num_threads=self.num_threads
        )
        sorted_ids = predictions.argsort()[::-1]
        sorted_items = [
            (self.i_mapping_rev[x], predictions[x], self.item_type_map[self.i_mapping_rev[x]]) for x in sorted_ids
        ]
        best_with_score = []
        item_types = [
            'container_course', 'container_workspace', 'engage_article', 'engage_microlearning', 'totara_playlist'
        ]
        for i_type in item_types:
            type_recommended = [(x[0], x[1]) for x in sorted_items if x[2] == i_type]
            type_recommended = type_recommended[:self.num_items]
            best_with_score.extend(type_recommended)

        return best_with_score

    def all_items(self):
        """
        This method generates a pandas dataframe using the instance variables of the class
        :return: A dataframe containing `uid` (user id), `totara_iid` (recommended Totara item ids) and `ranking`
        :rtype: DataFrame
        """
        user_items = []
        for totara_uid, internal_uid in self.u_mapping.items():
            might_like_items = self.__get_items(internal_uid=internal_uid)
            for item in might_like_items:
                user_items.append(
                    {
                        'uid': totara_uid,
                        'iid': item[0],
                        'ranking': item[1]
                    }
                )
        return pd.DataFrame(user_items)
