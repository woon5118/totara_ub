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

from lightfm import LightFM


class BuildModel:
    """
    This is a conceptual representation of the model build process
    """
    def __init__(
            self,
            interactions=None,
            weights=None,
            item_features=None,
            num_threads=2,
            optimized_hparams=None,
            item_alpha=0.0
    ):
        """
        Class constructor method
        :param interactions: the matrix containing user-item interactions of shape `[n_users, n_items]`,
            defaults to None
        :type interactions: np.float32 coo_matrix, mandatory
        :param weights: A matrix with entries expressing weights of individual interactions from the interactions
            matrix. Its row and col arrays must be the same as those of the interactions matrix, defaults to None
        :type weights: coo_matrix, mandatory
        :param item_features: A matrix of shape `[n_items, n_item_features]` where each row contains that item's weight
            over features, defaults to None
        :type item_features: csr_matrix, mandatory
        :param num_threads: Number of parallel computation threads to use. Should not be higher than the number of
            physical cores, defaults to 2
        :type num_threads: int, optional
        :param optimized_hparams: Optimized set of hyper-parameters; `epochs` and `num_threads`
        :type optimized_hparams: dict, mandatory
        """
        self.interactions = interactions
        self.weights = weights
        self.item_features = item_features
        self.num_threads = num_threads
        self.optimized_hparams = optimized_hparams
        self.item_alpha = item_alpha

    def build_model(self):
        """
        Uses instance variables to build the LightFM model object on the entire training set
        :return: LightFM model object
        """
        model = LightFM(
            loss='warp',
            item_alpha=self.item_alpha,
            learning_schedule='adadelta',
            no_components=self.optimized_hparams['no_components']
        )
        model.fit(
            interactions=self.interactions,
            sample_weight=self.weights,
            user_features=None,
            item_features=self.item_features,
            epochs=self.optimized_hparams['epochs'],
            num_threads=self.num_threads
        )

        return model
