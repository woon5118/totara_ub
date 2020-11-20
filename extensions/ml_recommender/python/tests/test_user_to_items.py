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

import unittest
from unittest.mock import Mock
import random
import numpy as np
from pandas import DataFrame
from subroutines.user_to_items import UserToItems


class TestUserToItems(unittest.TestCase):
    """
    This class is set up to test units of the `UserToItems` class
    """
    def setUp(self):
        """
        Hook method for setting up the fixture before exercising it
        """
        test_users_n = 10
        self.test_items_n = 20
        model = Mock()
        self.test_prediction = np.random.rand(self.test_items_n)
        model.predict.return_value = self.test_prediction
        types_choices = [
            'container_course', 'container_workspace', 'engage_article', 'engage_microlearning',
            'totara_playlist'
        ]
        u_mapping = dict(zip(range(2, (test_users_n + 2)), range(test_users_n)))
        self.i_mapping = dict(
            zip(['item' + str(i) for i in range(1, (self.test_items_n + 1))], range(self.test_items_n))
        )
        self.i_type_map = dict(
            zip(
                ['item' + str(i) for i in range(1, (self.test_items_n + 1))],
                random.choices(population=types_choices, k=self.test_items_n)
            )
        )
        self.user_to_items = UserToItems(
            u_mapping=u_mapping, i_mapping=self.i_mapping, model=model, item_type_map=self.i_type_map
        )

    def test_get_items(self):
        """
        This method tests if the `__get_items` method of the 'UserToItems` class returns the items as expected and the
        `predict` method of the `LightFM` class has been called once.
        """
        test_internal_uid = random.choice(list(self.i_mapping.values()))
        sorted_ids = self.test_prediction.argsort()[::-1]
        i_mapping_rev = {v: k for k, v in self.i_mapping.items()}
        sorted_items = [
            (i_mapping_rev[x], self.test_prediction[x], self.i_type_map[i_mapping_rev[x]]) for x in sorted_ids
        ]
        best_with_score = []
        item_types = [
            'container_course', 'container_workspace', 'engage_article', 'engage_microlearning', 'totara_playlist'
        ]
        for i_type in item_types:
            type_recommended = [(x[0], x[1]) for x in sorted_items if x[2] == i_type]
            type_recommended = type_recommended[:self.user_to_items.num_items]
            best_with_score.extend(type_recommended)
        the_ans = self.user_to_items._UserToItems__get_items(internal_uid=test_internal_uid)
        self.user_to_items.model.predict.assert_called_once()
        self.assertEqual(the_ans, best_with_score)

    def test_all_items(self):
        """
        This method tests if the `all_items` method of the `UserToItems` class returns a pandas` DataFrame object
        with the correct shape
        """
        computed_user_items = self.user_to_items.all_items()
        self.assertIsInstance(computed_user_items, DataFrame)
        self.assertEqual(computed_user_items.shape, (self.test_items_n * self.user_to_items.num_items, 3))
