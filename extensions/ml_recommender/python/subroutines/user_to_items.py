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
            positive_inter_map=None,
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
        :param positive_inter_map: A dictionary where keys are the Totara user ids and values are lists of the
            Totara item ids that user has interacted with
        :type positive_inter_map: dict
        :param model: The model to be evaluated
        :type model: LightFM model instance
        :param num_items: Number of top ranked items user wants to be recommended, defaults to 10
        :type num_items: int, optional
        :param num_threads: Number of parallel computation threads to use, defaults to 2
        :type num_items: int, optional
        """
        self.u_mapping = u_mapping
        self.i_mapping = i_mapping
        self.u_mapping_rev = {v: k for k, v in u_mapping.items()}
        self.i_mapping_rev = {v: k for k, v in i_mapping.items()}
        self.item_type_map = item_type_map
        self.item_features = item_features
        self.positive_inter_map = positive_inter_map
        self.model = model
        self.num_items = num_items
        self.num_threads = num_threads

    def __top_x_by_cat(self, sorted_items):
        """
        Returns top `num_items` recommended items where `num_items` is the instance variable of the class
        :param sorted_items: A list consisting of tuples where each tuple is composed of three items;
            Totara item id, ranking, and the item type
        :type sorted_items: list
        :return: A list of tuples where the first elements are the Totara ids of the items and the second
            ones the ranking.
        :rtype: list
        """
        best_with_score = []
        item_types = [
            'container_course', 'container_workspace', 'engage_article', 'engage_microlearning', 'totara_playlist'
        ]
        for i_type in item_types:
            type_recommended = [(x[0], x[1]) for x in sorted_items if x[2] == i_type][:self.num_items]
            best_with_score.extend(type_recommended)
        return best_with_score

    def __get_items(self, internal_uid, reduction_percentage=0.5):
        """
        Returns top `num_items` recommended items where `num_items` is the instance variable of the class
        :param internal_uid: The internal id of the user for whom the recommendations are sought
        :type internal_uid: int
        :param reduction_percentage: The percentage of the range of unseen item's recommendation score by
            which the seen item's recommendation score will be reduced, defaults to 0.5
        :type reduction_percentage: float, optional
        :return: A list of tuples where the first elements are the Totara ids of the items and the second
            ones the ranking.
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
        seen_totara_id = []
        if self.u_mapping_rev[internal_uid] in self.positive_inter_map:
            seen_totara_id = self.positive_inter_map[self.u_mapping_rev[internal_uid]]
        seen_internal_id = [self.i_mapping[x] for x in seen_totara_id]
        unseen_internal_id = [x for x in range(predictions.shape[0]) if x not in seen_internal_id]
        unseen_range = predictions[unseen_internal_id].max() - predictions[unseen_internal_id].min()
        for j in range(predictions.shape[0]):
            if j in seen_internal_id:
                predictions[j] = predictions[j] - unseen_range * reduction_percentage
        sorted_ids = predictions.argsort()[::-1]
        sorted_items = [
            (self.i_mapping_rev[x], predictions[x], self.item_type_map[self.i_mapping_rev[x]]) for x in sorted_ids
        ]
        best_with_score = self.__top_x_by_cat(sorted_items)
        return best_with_score

    def all_items(self):
        """
        This method generates a pandas dataframe using the instance variables of the class
        :return: A dataframe containing `uid` (user id), `totara_iid` (recommended Totara item ids) and `ranking`
        :rtype: DataFrame
        """
        user_items = []
        for totara_uid, internal_uid in self.u_mapping.items():
            might_like_items = self.__get_items(internal_uid=internal_uid, reduction_percentage=0.3)
            for item in might_like_items:
                user_items.append(
                    {
                        'uid': totara_uid,
                        'iid': item[0],
                        'ranking': item[1]
                    }
                )
        return pd.DataFrame(user_items)
