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


conf = {
    # Minimum number of users and items in interactions set for whom to run the recommendation engine in a tenant
    'min_data': {
        'min_users': 10,
        'min_items': 10
    },
    # The L2 penalty on item features when item features are being used in the model
    'item_alpha': 1e-6
}


class Config:
    """
    This is a conceptual representation of accessing the configuration elements from the conf object
    """
    def __init__(self):
        """
        The constructor method
        """
        self._config = conf

    def get_property(self, property_name):
        """
        This method accesses and returns the called item of the `conf` dictionary. The method returns `None` when the
            provided key does not match with any key of the `conf` dictionary
        :param property_name: A key from the keys of the `conf` dictionary
        :type property_name: str
        :return: An item from the `conf` dictionary whose key was used as input
        """
        value = None
        if property_name in self._config.keys():
            value = self._config[property_name]
        return value
