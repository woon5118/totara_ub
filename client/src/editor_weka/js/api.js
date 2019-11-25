/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD's customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module editor_weka
 */

import apollo from 'tui/apollo_client';
import getRepositoryData from 'editor_weka/graphql/get_repository_data';
import linkMetadataQuery from 'core/graphql/get_linkmetadata';
import getMediaTypeQuery from 'editor_weka/graphql/media_type_of_draft_file';

/**
 * Get Open Graph metadata for the provided URL.
 *
 * @param {string} url
 * @returns {object}
 */
export async function getLinkMetadata(url) {
  const result = await apollo.query({
    query: linkMetadataQuery,
    variables: { url },
  });
  return result.data.link;
}

/**
 *
 * @param {Number|null} contextId
 *
 * @return {Promise<any>}
 */
export async function prepareDraftFileArea(contextId = null) {
  const {
    data: { repository_data },
  } = await apollo.query({
    query: getRepositoryData,
    variables: { context_id: contextId },
    fetchPolicy: 'no-cache',
  });

  return repository_data;
}

/**
 *
 * @param {Number}  itemId
 * @param {String}  filename
 *
 * @return {Promise}
 */
export async function getMediaType({ itemId, filename }) {
  const {
    data: {
      file: { media_type, mime_type },
    },
  } = await apollo.query({
    query: getMediaTypeQuery,
    variables: {
      item_id: itemId,
      filename: filename,
    },
  });

  return {
    mediaType: media_type,
    mimeType: mime_type,
  };
}
