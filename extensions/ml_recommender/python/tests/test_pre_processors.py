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
import lorem
from subroutines.pre_processors import PreProcessors


class TestPreProcessors(unittest.TestCase):
    """
    This object is set up to test units of the `PreProcessors` class
    """
    def setUp(self):
        """
        Hook method for setting up the fixture before exercising it
        """
        self.raw_doc = lorem.paragraph()
        self.pre_processor = PreProcessors()

    def test_remove_urls(self):
        """
        This method tests if the `__remove_urls` method of the `PreProcessors` class removes urls from the text
        documents
        """
        test_doc = 'The website address of Totara Learning Solutions Ltd is https://www.totaralearning.com/'
        returned_doc = self.pre_processor._PreProcessors__remove_urls(in_doc=test_doc)
        url_removed_doc = 'The website address of Totara Learning Solutions Ltd is'
        self.assertEqual(returned_doc, url_removed_doc)

    def test_lower_applied(self):
        """
        This method tests if the `preprocess_docs` method of the `PreProcessors` class changes the upper case characters
        to the lower case ones.
        """
        test_doc = 'The website address of Totara Learning Solutions Ltd is https://www.totaralearning.com/'
        processed_doc = self.pre_processor.preprocess_docs(raw_doc=test_doc)
        lowered_doc = 'the website address of totara learning solutions ltd is'
        self.assertEqual(processed_doc, lowered_doc)

    def test_numerics_removed(self):
        """
        This method tests if the `preprocess_docs` method of the `PreProcessors` class removes the numeric characters
        from the given text
        """
        test_doc = 'The eighteenth birthday happens when you turn 18'
        processed_doc = self.pre_processor.preprocess_docs(raw_doc=test_doc)
        without_numerics_doc = 'the eighteenth birthday happens when you turn'
        self.assertEqual(processed_doc, without_numerics_doc)

    def test_whitespaces_removed(self):
        """
        This method tests if the `preprocess_docs` method of the `PreProcessors` class removes the white spaces
        from the given text
        """
        test_doc = '  A quick   brown fox jumps over the lazy    dog '
        processed_doc = self.pre_processor.preprocess_docs(raw_doc=test_doc)
        spaces_removed_doc = 'a quick brown fox jumps over the lazy dog'
        self.assertEqual(processed_doc, spaces_removed_doc)
