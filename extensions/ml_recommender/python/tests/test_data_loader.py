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
import os
import pandas as pd
import random
from scipy.sparse import coo_matrix, csr_matrix
from subroutines.data_loader import DataLoader
from tests.generate_data import GenerateData


class TestDataLoader(unittest.TestCase):
    """
    This class is the test object to test units of the class `DataLoader`
    """
    def setUp(self):
        """
        Hook method for setting up the fixture before exercising it
        """
        data_obj = GenerateData()
        self.interactions = data_obj.get_interactions()
        self.items_data = data_obj.get_items()
        self.users_data = data_obj.get_users()
        nl_libs = os.path.join(
            os.path.dirname(os.path.dirname(os.path.abspath(__file__))), 'totara'
        )
        self.data_loader = DataLoader(nl_libs=nl_libs)

    def test_get_interactions(self):
        """
        This method tests if the `__get_interactions` method of the `DataLoader` class
        returns a list object, the size of the list is as expected, each element
        of the returned list is a tuple and the length of each tuple in the list is 3.
        """
        interactions = self.data_loader._DataLoader__get_interactions(
            interactions=self.interactions
        )
        self.assertIsInstance(interactions, list)
        self.assertEqual(self.interactions.shape[0], len(interactions))
        self.assertEqual(len(random.choice(interactions)), 3)
        self.assertIsInstance(random.choice(interactions), tuple)

    def test_get_users(self):
        """
        This method tests if the `__get_users` method of the `DataLoader` class returns a list object
        and length of the list is as expected.
        """
        users_data = self.data_loader._DataLoader__get_users(
            users_data=self.users_data
        )
        self.assertIsInstance(users_data, list)
        self.assertEqual(self.users_data.shape[0], len(users_data))

    def test_create_feature_dict(self):
        """
        This method tests if the `__create_feature_dict` method of the `DataLoader` class returns
        a dictionary object and the contents of the dictionary are as expected
        """
        size = 20
        test_vals = random.choices(population=[0, 1], k=size)
        test_features = ['feature_' + str(i) for i in range(size)]
        test_series = pd.Series(
            data=test_vals,
            index=test_features
        )
        indices_1s = [i for i, x in enumerate(test_vals) if x == 1]
        vals_1s = [x for x in test_vals if x == 1]
        features_1s = [test_features[i] for i in indices_1s]
        test_dict = dict(zip(features_1s, vals_1s))
        computed_dict = self.data_loader._DataLoader__create_feature_dict(
            row=test_series
        )
        self.assertIsInstance(computed_dict, dict)
        self.assertEqual(computed_dict, test_dict)

    def test_create_partial_features(self):
        """
        This method tests if the `__create_partial_features` method of the `DataLoader` class
        returns a list, has correct length, each element of the list is a tuple, the length
        of each tuple in the list is 2, and the second element of the tuple is a dictionary.
        """
        test_items_df = self.items_data.drop(labels='document', axis=1)
        computed_items_features = self.data_loader._DataLoader__create_partial_features(
            test_items_df
        )
        self.assertIsInstance(computed_items_features, list)
        self.assertEqual(test_items_df.shape[0], len(computed_items_features))
        self.assertIsInstance(random.choice(computed_items_features), tuple)
        self.assertEqual(len(random.choice(computed_items_features)), 2)
        self.assertIsInstance(random.choice(computed_items_features)[1], dict)

    def test_get_items_attr(self):
        """
        This method tests if the `__get_item_attr` method of the `DataLoader` class returns a
        dictionary, and has the same length as the item number.
        """
        columns_req = [
            'container_course', 'container_workspace', 'engage_article', 'engage_microlearning', 'totara_playlist'
        ]
        test_items_df = self.items_data.drop(labels=self.items_data.columns.difference(columns_req), axis=1)
        computed_items_attr = self.data_loader._DataLoader__get_items_attr(dataframe=test_items_df)
        self.assertIsInstance(computed_items_attr, dict)
        self.assertEqual(test_items_df.shape[0], len(computed_items_attr))

    def test_get_items(self):
        """
        This method tests if the `__get_items` method of the `DataLoader` class returns a dictionary,
        which has the items `items_features_data`, `features_list`, `item_ids`, and `item_type_map`.
        It tests if the `items_features_data` item of the returned dictionary is a list and has the
        correct length. It tests if the `item_ids` item of the returned dictionary is a list and has
        string elements in it. It tests if the `item_type_map` element of the returned dictionary
        is a dictionary, is of correct length and its keys are as expected. It further tests that
        the `items_features_data` is as expected based on the input argument `query` of the method.
        """
        for query in ['partial', 'hybrid', 'mf']:
            items_transformed_data = self.data_loader._DataLoader__get_items(
                items_data=self.items_data, query=query
            )
            self.assertIsInstance(items_transformed_data, dict)
            self.assertIn('items_features_data', items_transformed_data)
            self.assertIn('features_list', items_transformed_data)
            self.assertIn('item_ids', items_transformed_data)
            self.assertIn('item_type_map', items_transformed_data)
            self.assertIsInstance(items_transformed_data['item_ids'], list)
            self.assertIsInstance(random.choice(items_transformed_data['item_ids']), str)
            self.assertIsInstance(items_transformed_data['item_type_map'], dict)
            self.assertEqual(len(items_transformed_data['item_type_map']), self.items_data.shape[0])
            self.assertIn(random.choice(self.items_data.index), items_transformed_data['item_type_map'])
            if query == 'mf':
                self.assertIsNone(items_transformed_data['items_features_data'])
            else:
                self.assertIsInstance(items_transformed_data['items_features_data'], list)
                self.assertIsInstance(random.choice(items_transformed_data['items_features_data']), tuple)
                self.assertEqual(len(random.choice(items_transformed_data['items_features_data'])), 2)
                self.assertIsInstance(random.choice(items_transformed_data['items_features_data'])[1], dict)
                self.assertIsInstance(items_transformed_data['features_list'], list)
                self.assertIsInstance(random.choice(items_transformed_data['features_list']), str)

    def test_transform_data(self):
        """
        This method tests that the `__transform_data` method of the `DataLoader` class returns a dictionary
        object containing the correct items of correct shapes as expected.
        """
        for query in ['hybrid', 'partial', 'mf']:
            transformed_data = self.data_loader._DataLoader__transform_data(
                interactions=self.interactions,
                items_data=self.items_data,
                users_data=self.users_data,
                query=query
            )
            self.assertIsInstance(transformed_data, dict)
            self.assertIsInstance(transformed_data['interactions'], list)
            self.assertEqual(len(transformed_data['interactions']), self.interactions.shape[0])
            self.assertIsInstance(transformed_data['items_data'], dict)
            self.assertIsInstance(transformed_data['user_ids'], list)
            self.assertEqual(len(transformed_data['user_ids']), self.users_data.shape[0])

    def test_load_data(self):
        """
        This method tests if the `load_data` method of the `DataLoader` class returns a dictionary containing the
        correct elements, with the correct types and shapes based on the `query` argument of the method.
        """
        for query in ['hybrid', 'partial', 'mf']:
            loaded_data = self.data_loader.load_data(
                interactions=self.interactions,
                items_data=self.items_data,
                users_data=self.users_data,
                query=query
                )
            self.assertIsInstance(loaded_data, dict)
            self.assertIn('interactions', loaded_data)
            self.assertIn('weights', loaded_data)
            self.assertIn('item_features', loaded_data)
            self.assertIn('mapping', loaded_data)
            self.assertIn('item_type_map', loaded_data)
            self.assertIsInstance(loaded_data['interactions'], coo_matrix)
            self.assertIsInstance(loaded_data['weights'], coo_matrix)
            self.assertIsInstance(loaded_data['mapping'], tuple)
            self.assertEqual(len(loaded_data['mapping'][0]), self.users_data.shape[0])
            self.assertEqual(len(loaded_data['mapping'][2]), self.items_data.shape[0])
            self.assertIsInstance(loaded_data['item_type_map'], dict)
            self.assertEqual(len(loaded_data['item_type_map']), self.items_data.shape[0])
            if query == 'mf':
                self.assertIsNone(loaded_data['item_features'])
            else:
                self.assertIsInstance(loaded_data['item_features'], csr_matrix)
