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

import re


class PreProcessors:
    """
    This is a conceptual representation of cleaning the document strings
    """
    def __init__(self, stopwords=None):
        """
        Class constructor method
        :param stopwords: A list of stopwords. These stopwords whenever provided must belong to
            the same language as the `raw_doc`, defaults to None
        :type stopwords: list, optional
        """
        self.stopwords = stopwords

    @staticmethod
    def __remove_urls(in_doc=None):
        """
        This static method removes part of string that matches with a URL pattern
        :param in_doc: The document string from which the URL needs to be removed, defaults to None
        :type in_doc: str, mandatory
        :return: A string after URLs have been removed
        :rtype: str
        """
        reg_patt = r"(@[A-Za-z0-9]+)|([^0-9A-Za-z \t])|(\w+://\S+)"
        new_doc = ' '.join(re.sub(reg_patt, " ", in_doc).split())
        return new_doc

    def preprocess_docs(self, raw_doc=None):
        """
        This method preprocesses the strings and returns a string that is in lower case, has URLs removed,
        numeric characters removed, has no stopwords, and has been lemmatized.
        :param raw_doc: The document to be preprocessed/cleaned, defaults to None
        :type raw_doc: str, mandatory
        :return: A preprocessed document
        :rtype: str
        """
        # Convert the document to the lower case
        doc = raw_doc.lower()  # type, str
        # Remove the URLs
        doc = self.__remove_urls(in_doc=doc)  # type, str
        # Remove the numeric characters
        doc = re.sub(r'\d+', '', doc)  # type, str
        # Remove punctuations with white spaces
        doc = re.sub(r'[^\w\s]', ' ', doc)  # type, str
        # Replace multiple white spaces with single one
        doc = ' '.join(doc.split(sep=' '))
        # Remove the trailing and leading white spaces
        doc = doc.strip()
        # Convert the document string into list of words
        doc = doc.split(sep=' ')  # type, list
        # Remove stopwords
        if self.stopwords is not None:
            doc = [t for t in doc if t not in self.stopwords]  # type, list
        else:
            doc = doc  # type, list
        # Join the words into a single string of document
        doc = ' '.join(doc)  # type, str
        return doc
