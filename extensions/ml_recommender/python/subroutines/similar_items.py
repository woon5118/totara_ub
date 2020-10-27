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
from operator import itemgetter


class SimilarItems:
    """
    This is a conceptual representation for generating the list of similar items for the items
    """
    def __init__(self, mapping=None, item_representations=None, num_items=10):
        """
        Constructor method
        :param mapping: A dictionary where keys are Totara item ids and values are internal item ids, defaults to None
        :type mapping: dict, mandatory
        :param item_representations: The latent representations of the items in the shape `[n_items, num_components]`,
            defaults to None
        :type item_representations: np.float32 array, mandatory
        :param num_items: The number of similar item recommendations for each item, defaults to None
        :type num_items: int, optional
        """
        self.mapping = mapping
        self.item_representations = item_representations
        self.num_items = num_items

    def __get_items(self, internal_idx):
        """
        Returns `num_items` number of items (as defined in the class constructor method) similar
        to the item with internal id `internal_idx`
        :param internal_idx: Internal id of the item whose similar items are being sought
        :type internal_idx: int, mandatory
        :return: A list of (`iternal_id`, `similarity_score`) of `num_items` that are similar to the given item
        :rtype: list
        """
        # Cosine similarity
        scores = self.item_representations.dot(self.item_representations[internal_idx, :])
        item_norms = np.linalg.norm(self.item_representations, axis=1)
        scores /= item_norms

        best = [item[0] for item in sorted(enumerate(scores), key=itemgetter(1), reverse=True)[:self.num_items]]
        sorted_best = zip(best, scores[best] / item_norms[internal_idx])

        sorted_best = [(item[0], item[1]) for item in sorted_best if item[0] != internal_idx]
        return sorted_best

    def all_items(self):
        """
        Loops through each `target_iid` (target internal id) of items and creates a list of
        `num_items` (number of similar items defined in the class constructor method).
        :return: A pandas DataFrame with three columns `target_iid`, `similar_iid`, and `ranking`
        :rtype: DataFrame
        """
        similar_items = []
        mapping_rev = {v: k for k, v in self.mapping.items()}
        for totara_id, internal_id in self.mapping.items():
            i_similar = self.__get_items(internal_idx=internal_id)
            for item in i_similar:
                similar_items.append(
                    {
                        'target_iid': totara_id,
                        'similar_iid': mapping_rev[item[0]],
                        'ranking': item[1]
                    }
                )
        return pd.DataFrame(similar_items)
