/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
 */

import { announce } from 'totara_core/accessibility';
import { getString, langString } from 'totara_core/i18n';

export default class DragDropAnnouncer {
  constructor(manager) {
    this.manager = manager;
  }

  handleDragStart({ dropDesc }) {
    announce({
      // message: `You have lifted an item at position ${dropDesc.index + 1}.`,
      message: getString('dragdrop_announce_lift', 'totara_core', {
        from_position: dropDesc.index + 1,
      }),
    });
  }

  handleDragMove({ dragItem, dropDesc, valid }) {
    if (!dropDesc) {
      announce({
        message: getString('dragdrop_announce_no_drop', 'totara_core'),
      });
      return;
    }

    const fromSource = this.manager.getSource(dragItem.descriptor.sourceId);
    const toSource = this.manager.getSource(dropDesc.sourceId);

    const formatParams = {
      from_position: dragItem.descriptor.index + 1,
      from_source_name: fromSource && fromSource.sourceName,
      to_position: dropDesc.index + 1,
      to_source_name: toSource && toSource.sourceName,
    };

    const invalidAppend = valid
      ? ''
      : ' ' + getString('dragdrop_announce_no_drop', 'totara_core');

    if (fromSource == toSource) {
      announce({
        message:
          getString(
            'dragdrop_announce_move_same',
            'totara_core',
            formatParams
          ) + invalidAppend,
      });
    } else {
      // moving lists via keyboard while in drag mode is not supported at the
      // moment, but handle it anyway
      if (
        fromSource &&
        toSource &&
        fromSource.sourceName &&
        toSource.sourceName
      ) {
        announce({
          message:
            getString(
              'dragdrop_announce_move_other',
              'totara_core',
              formatParams
            ) + invalidAppend,
        });
      } else {
        announce({
          message:
            getString(
              'dragdrop_announce_move_unknown',
              'totara_core',
              formatParams
            ) + invalidAppend,
        });
      }
    }
  }

  handleDragEnd({ dragItem, dropDesc, drop }) {
    const fromSource = this.manager.getSource(dragItem.descriptor.sourceId);
    const toSource = this.manager.getSource(dropDesc.sourceId);
    const formatParams = {
      from_position: dragItem.descriptor.index + 1,
      from_source_name: fromSource && fromSource.sourceName,
      to_position: dropDesc.index + 1,
      to_source_name: toSource && toSource.sourceName,
    };

    if (!drop) {
      announce({
        message: getString(
          'dragdrop_announce_drop_cancel',
          'totara_core',
          formatParams
        ),
      });
      return;
    }

    if (drop) {
      if (fromSource == toSource) {
        announce({
          message: getString(
            'dragdrop_announce_drop_same',
            'totara_core',
            formatParams
          ),
        });
      } else {
        if (fromSource.sourceName && toSource.sourceName) {
          announce({
            message: getString(
              'dragdrop_announce_drop_other',
              'totara_core',
              formatParams
            ),
          });
        } else {
          announce({
            message: getString(
              'dragdrop_announce_drop_unknown',
              'totara_core',
              formatParams
            ),
          });
        }
      }
    }
  }
}

DragDropAnnouncer.langStrings = [
  langString('dragdrop_announce_lift', 'totara_core'),
  langString('dragdrop_announce_move_same', 'totara_core'),
  langString('dragdrop_announce_move_other', 'totara_core'),
  langString('dragdrop_announce_move_unknown', 'totara_core'),
  langString('dragdrop_announce_no_drop', 'totara_core'),
  langString('dragdrop_announce_drop_cancel', 'totara_core'),
  langString('dragdrop_announce_drop_same', 'totara_core'),
  langString('dragdrop_announce_drop_other', 'totara_core'),
  langString('dragdrop_announce_drop_unknown', 'totara_core'),
];
