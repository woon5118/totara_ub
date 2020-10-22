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
import pandas as pd


class GetTenants:
    """
    This is a conceptual representation of reading the list of tenants on the Totara instance from the
    `tenants.csv` file.
    """
    def __init__(self, data_home=None):
        """
        Class constructor method
        :param data_home: Full path to the directory containing the Totara exported csv files, defaults to None
        :type data_home: str, mandatory
        """
        self.data_home = data_home

    def get_tenants(self):
        """
        Uses class instance variables to read the `tenants.csv` file to read the tenants
        :return: The tenants on the Totara instance
        :rtype: list
        """
        file_path = os.path.join(self.data_home, 'tenants.csv')
        if os.path.exists(file_path):
            tenants = pd.read_csv(filepath_or_buffer=file_path, sep=',', encoding='utf-8')
            if tenants.shape[0] == 0:
                result = [0]
            else:
                result = tenants['tenants'].tolist()
        else:
            result = [0]
        return result
