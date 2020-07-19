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
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @module tui
 */

import { langString } from './i18n';

/**
 * Return array of available time Zone keys with a corresponding language string.
 *
 * List is based of time zones from IANA database
 * (Excludes times zones without existing strings in lib/timezones)
 *
 * Excluded time zones ID's
 * Africa:
 * Africa/Juba
 *
 * America:
 * America/Argentina/Salta, America/Argentina/San_Luis, America/Atikokan
 * America/Atka, America/Bahia_Banderas, America/Blanc-Sablon, America/Buenos_Aires,
 * America/Catamarca, America/Coral_Harbour, America/Cordoba, America/Creston,
 * America/Ensenada, America/Fort_Nelson, America/Fort_Wayne, America/Indiana/Petersburg,
 * America/Indiana/Tell_City, America/Indiana/Vincennes, America/Indiana/Winamac,
 * America/Jujuy, America/Kentucky/Louisville, America/Knox_IN, America/Kralendijk,
 * America/Lower_Princes, America/Marigot, America/Matamoros, America/Mendoza,
 * America/Metlakatla, America/Moncton, America/North_Dakota/Beulah,
 * America/North_Dakota/New_Salem, America/Ojinaga, America/Porto_Acre
 * America/Punta_Arenas, America/Resolute, America/Rosario, America/Santa_Isabel,
 * America/Santarem, America/Shiprock, America/Sitka, America/St_Barthelemy,
 * America/Virgin
 *
 * Antarctica:
 * Antarctica/Macquarie, Antarctica/South_Pole, Antarctica/Troll
 *
 * Arctic:
 * Arctic/Longyearbyen
 *
 * Asia:
 * Asia/Ashkhabad, Asia/Atyrau, Asia/Barnau, Asia/Chita, Asia/Chungking, Asia/Dacca,
 * Asia/Famagusta, Asia/Hebron, Asia/Ho_Chi_Minh, Asia/Istanbul, Asia/Jakarta,
 * Asia/Kathmandu, Asia/Khandyga, Asia/Macao, Asia/Novokuznetsk, Asia/Srednekolymsk,
 * Asia/Tel_Aviv, Asia/Thimbu, Asia/Tomsk, Asia/Ujung_Pandang, Asia/Ulan_Bator,
 * Asia/Ust-Nera, Asia/Yangon
 *
 * Atlantic:
 * Atlantic/Faroe, Atlantic/Jan_Mayen
 *
 * Australia:
 * Australia/Canberra, Australia/Currie, Australia/Eucla, Australia/Yancowinna
 *
 * GMT:
 * Etc/GMT, Etc/GMT+1, Etc/GMT+2, Etc/GMT+3, Etc/GMT+4, Etc/GMT+5, Etc/GMT+6,
 * Etc/GMT+7, Etc/GMT+8, Etc/GMT+9, Etc/GMT+10, Etc/GMT+11, Etc/GMT+12, Etc/GMT-1
 * Etc/GMT-2, Etc/GMT-3, Etc/GMT-4, Etc/GMT-5, Etc/GMT-6, Etc/GMT-7, Etc/GMT-8,
 * Etc/GMT-9, Etc/GMT-10, Etc/GMT-11, Etc/GMT-12, Etc/GMT-13, Etc/GMT-14
 *
 * Europe:
 * Europe/Astrakhan, Europe/Bratislava, Europe/Busingen, Europe/Guernsey,
 * Europe/Isle_of_Man, Europe/Jersey, Europe/Kirov, 'Europe/Ljubljana',
 * Europe/Mariehamn, Europe/Podgorica, Europe/San_Marino, Europe/Sarajevo,
 * Europe/Saratov, Europe/Skopje, Europe/Tiraspol, Europe/Ulyanovsk,
 * Europe/Vatican, Europe/Volgograd, Europe/Zagreb
 *
 * Indian:
 * Indian/Cocos
 *
 * Pacific:
 * Pacific/Bougainville, Pacific/Chuuk, Pacific/Johnston, Pacific/Pohnpei,
 * Pacific/Samoa
 *
 * @returns {array}
 */
export function getTimeZoneKeyStrings() {
  const zones = [
    {
      id: 'Africa/Abidjan',
      label: langString('africa/abidjan', 'timezones'),
    },
    {
      id: 'Africa/Accra',
      label: langString('africa/accra', 'timezones'),
    },
    {
      id: 'Africa/Addis_Ababa',
      label: langString('africa/addis_ababa', 'timezones'),
    },
    {
      id: 'Africa/Algiers',
      label: langString('africa/algiers', 'timezones'),
    },
    {
      id: 'Africa/Asmara',
      label: langString('africa/asmera', 'timezones'),
    },
    {
      id: 'Africa/Bamako',
      label: langString('africa/bamako', 'timezones'),
    },
    {
      id: 'Africa/Bangui',
      label: langString('africa/bangui', 'timezones'),
    },
    {
      id: 'Africa/Banjul',
      label: langString('africa/banjul', 'timezones'),
    },
    {
      id: 'Africa/Bissau',
      label: langString('africa/bissau', 'timezones'),
    },
    {
      id: 'Africa/Blantyre',
      label: langString('africa/blantyre', 'timezones'),
    },
    {
      id: 'Africa/Brazzaville',
      label: langString('africa/brazzaville', 'timezones'),
    },
    {
      id: 'Africa/Bujumbura',
      label: langString('africa/bujumbura', 'timezones'),
    },
    {
      id: 'Africa/Cairo',
      label: langString('africa/cairo', 'timezones'),
    },
    {
      id: 'Africa/Casablanca',
      label: langString('africa/casablanca', 'timezones'),
    },
    {
      id: 'Africa/Ceuta',
      label: langString('africa/ceuta', 'timezones'),
    },
    {
      id: 'Africa/Conakry',
      label: langString('africa/conakry', 'timezones'),
    },
    {
      id: 'Africa/Dakar',
      label: langString('africa/dakar', 'timezones'),
    },
    {
      id: 'Africa/Dar_es_Salaam',
      label: langString('africa/dar_es_salaam', 'timezones'),
    },
    {
      id: 'Africa/Djibouti',
      label: langString('africa/djibouti', 'timezones'),
    },
    {
      id: 'Africa/Douala',
      label: langString('africa/douala', 'timezones'),
    },
    {
      id: 'Africa/El_Aaiun',
      label: langString('africa/el_aaiun', 'timezones'),
    },
    {
      id: 'Africa/Freetown',
      label: langString('africa/freetown', 'timezones'),
    },
    {
      id: 'Africa/Gaborone',
      label: langString('africa/gaborone', 'timezones'),
    },
    {
      id: 'Africa/Harare',
      label: langString('africa/harare', 'timezones'),
    },
    {
      id: 'Africa/Johannesburg',
      label: langString('africa/johannesburg', 'timezones'),
    },
    {
      id: 'Africa/Kampala',
      label: langString('africa/kampala', 'timezones'),
    },
    {
      id: 'Africa/Khartoum',
      label: langString('africa/khartoum', 'timezones'),
    },
    {
      id: 'Africa/Kigali',
      label: langString('africa/kigali', 'timezones'),
    },
    {
      id: 'Africa/Kinshasa',
      label: langString('africa/kinshasa', 'timezones'),
    },
    {
      id: 'Africa/Lagos',
      label: langString('africa/lagos', 'timezones'),
    },
    {
      id: 'Africa/Libreville',
      label: langString('africa/libreville', 'timezones'),
    },
    {
      id: 'Africa/Lome',
      label: langString('africa/lome', 'timezones'),
    },
    {
      id: 'Africa/Luanda',
      label: langString('africa/luanda', 'timezones'),
    },
    {
      id: 'Africa/Lubumbashi',
      label: langString('africa/lubumbashi', 'timezones'),
    },
    {
      id: 'Africa/Lusaka',
      label: langString('africa/lusaka', 'timezones'),
    },
    {
      id: 'Africa/Malabo',
      label: langString('africa/malabo', 'timezones'),
    },
    {
      id: 'Africa/Maputo',
      label: langString('africa/maputo', 'timezones'),
    },
    {
      id: 'Africa/Maseru',
      label: langString('africa/maseru', 'timezones'),
    },
    {
      id: 'Africa/Mbabane',
      label: langString('africa/mbabane', 'timezones'),
    },
    {
      id: 'Africa/Mogadishu',
      label: langString('africa/mogadishu', 'timezones'),
    },
    {
      id: 'Africa/Monrovia',
      label: langString('africa/monrovia', 'timezones'),
    },
    {
      id: 'Africa/Nairobi',
      label: langString('africa/nairobi', 'timezones'),
    },
    {
      id: 'Africa/Ndjamena',
      label: langString('africa/ndjamena', 'timezones'),
    },
    {
      id: 'Africa/Niamey',
      label: langString('africa/niamey', 'timezones'),
    },
    {
      id: 'Africa/Nouakchott',
      label: langString('africa/nouakchott', 'timezones'),
    },
    {
      id: 'Africa/Ouagadougou',
      label: langString('africa/ouagadougou', 'timezones'),
    },
    {
      id: 'Africa/Porto-Novo',
      label: langString('africa/porto-novo', 'timezones'),
    },
    {
      id: 'Africa/Sao_Tome',
      label: langString('africa/sao_tome', 'timezones'),
    },
    {
      id: 'Africa/Timbuktu',
      label: langString('africa/timbuktu', 'timezones'),
    },
    {
      id: 'Africa/Tripoli',
      label: langString('africa/tripoli', 'timezones'),
    },
    {
      id: 'Africa/Tunis',
      label: langString('africa/tunis', 'timezones'),
    },
    {
      id: 'Africa/Windhoek',
      label: langString('africa/windhoek', 'timezones'),
    },
    {
      id: 'America/Adak',
      label: langString('america/adak', 'timezones'),
    },
    {
      id: 'America/Anchorage',
      label: langString('america/anchorage', 'timezones'),
    },
    {
      id: 'America/Anguilla',
      label: langString('america/anguilla', 'timezones'),
    },
    {
      id: 'America/Antigua',
      label: langString('america/antigua', 'timezones'),
    },
    {
      id: 'America/Araguaina',
      label: langString('america/araguaina', 'timezones'),
    },
    {
      id: 'America/Argentina/Buenos_Aires',
      label: langString('america/argentina/buenos_aires', 'timezones'),
    },
    {
      id: 'America/Argentina/Catamarca',
      label: langString('america/argentina/catamarca', 'timezones'),
    },
    {
      id: 'America/Argentina/ComodRivadavia',
      label: langString('america/argentina/comodrivadavia', 'timezones'),
    },
    {
      id: 'America/Argentina/Cordoba',
      label: langString('america/argentina/cordoba', 'timezones'),
    },
    {
      id: 'America/Argentina/Jujuy',
      label: langString('america/argentina/jujuy', 'timezones'),
    },
    {
      id: 'America/Argentina/La_Rioja',
      label: langString('america/argentina/la_rioja', 'timezones'),
    },
    {
      id: 'America/Argentina/Mendoza',
      label: langString('america/argentina/mendoza', 'timezones'),
    },
    {
      id: 'America/Argentina/Rio_Gallegos',
      label: langString('america/argentina/rio_gallegos', 'timezones'),
    },
    {
      id: 'America/Argentina/San_Juan',
      label: langString('america/argentina/san_juan', 'timezones'),
    },
    {
      id: 'America/Argentina/Tucuman',
      label: langString('america/argentina/tucuman', 'timezones'),
    },
    {
      id: 'America/Argentina/Ushuaia',
      label: langString('america/argentina/ushuaia', 'timezones'),
    },
    {
      id: 'America/Aruba',
      label: langString('america/aruba', 'timezones'),
    },
    {
      id: 'America/Asuncion',
      label: langString('america/asuncion', 'timezones'),
    },
    {
      id: 'America/Bahia',
      label: langString('america/bahia', 'timezones'),
    },
    {
      id: 'America/Barbados',
      label: langString('america/barbados', 'timezones'),
    },
    {
      id: 'America/Belem',
      label: langString('america/belem', 'timezones'),
    },
    {
      id: 'America/Belize',
      label: langString('america/belize', 'timezones'),
    },
    {
      id: 'America/Boa_Vista',
      label: langString('america/boa_vista', 'timezones'),
    },
    {
      id: 'America/Bogota',
      label: langString('america/bogota', 'timezones'),
    },
    {
      id: 'America/Boise',
      label: langString('america/boise', 'timezones'),
    },
    {
      id: 'America/Cambridge_Bay',
      label: langString('america/cambridge_bay', 'timezones'),
    },
    {
      id: 'America/Campo_Grande',
      label: langString('america/campo_grande', 'timezones'),
    },
    {
      id: 'America/Cancun',
      label: langString('america/cancun', 'timezones'),
    },
    {
      id: 'America/Caracas',
      label: langString('america/caracas', 'timezones'),
    },
    {
      id: 'America/Cayenne',
      label: langString('america/cayenne', 'timezones'),
    },
    {
      id: 'America/Cayman',
      label: langString('america/cayman', 'timezones'),
    },
    {
      id: 'America/Chicago',
      label: langString('america/chicago', 'timezones'),
    },
    {
      id: 'America/Chihuahua',
      label: langString('america/chihuahua', 'timezones'),
    },
    {
      id: 'America/Costa_Rica',
      label: langString('america/costa_rica', 'timezones'),
    },
    {
      id: 'America/Cuiaba',
      label: langString('america/cuiaba', 'timezones'),
    },
    {
      id: 'America/Curacao',
      label: langString('america/curacao', 'timezones'),
    },
    {
      id: 'America/Danmarkshavn',
      label: langString('america/danmarkshavn', 'timezones'),
    },
    {
      id: 'America/Dawson',
      label: langString('america/dawson', 'timezones'),
    },
    {
      id: 'America/Dawson_Creek',
      label: langString('america/dawson_creek', 'timezones'),
    },
    {
      id: 'America/Denver',
      label: langString('america/denver', 'timezones'),
    },
    {
      id: 'America/Detroit',
      label: langString('america/detroit', 'timezones'),
    },
    {
      id: 'America/Dominica',
      label: langString('america/dominica', 'timezones'),
    },
    {
      id: 'America/Edmonton',
      label: langString('america/edmonton', 'timezones'),
    },
    {
      id: 'America/Eirunepe',
      label: langString('america/eirunepe', 'timezones'),
    },
    {
      id: 'America/El_Salvador',
      label: langString('america/el_salvador', 'timezones'),
    },
    {
      id: 'America/Fortaleza',
      label: langString('america/fortaleza', 'timezones'),
    },
    {
      id: 'America/Glace_Bay',
      label: langString('america/glace_bay', 'timezones'),
    },
    {
      id: 'America/Godthab',
      label: langString('america/godthab', 'timezones'),
    },
    {
      id: 'America/Goose_Bay',
      label: langString('america/goose_bay', 'timezones'),
    },
    {
      id: 'America/Grand_Turk',
      label: langString('america/grand_turk', 'timezones'),
    },
    {
      id: 'America/Grenada',
      label: langString('america/grenada', 'timezones'),
    },
    {
      id: 'America/Guadeloupe',
      label: langString('america/guadeloupe', 'timezones'),
    },
    {
      id: 'America/Guatemala',
      label: langString('america/guatemala', 'timezones'),
    },
    {
      id: 'America/Guayaquil',
      label: langString('america/guayaquil', 'timezones'),
    },
    {
      id: 'America/Guyana',
      label: langString('america/guyana', 'timezones'),
    },
    {
      id: 'America/Halifax',
      label: langString('america/halifax', 'timezones'),
    },
    {
      id: 'America/Havana',
      label: langString('america/havana', 'timezones'),
    },
    {
      id: 'America/Hermosillo',
      label: langString('america/hermosillo', 'timezones'),
    },
    {
      id: 'America/Indiana/Indianapolis',
      label: langString('america/indiana/indianapolis', 'timezones'),
    },
    {
      id: 'America/Indiana/Knox',
      label: langString('america/indiana/knox', 'timezones'),
    },
    {
      id: 'America/Indiana/Marengo',
      label: langString('america/indiana/marengo', 'timezones'),
    },
    {
      id: 'America/Indiana/Vevay',
      label: langString('america/indiana/vevay', 'timezones'),
    },
    {
      id: 'America/Indianapolis',
      label: langString('america/indianapolis', 'timezones'),
    },
    {
      id: 'America/Inuvik',
      label: langString('america/inuvik', 'timezones'),
    },
    {
      id: 'America/Iqaluit',
      label: langString('america/iqaluit', 'timezones'),
    },
    {
      id: 'America/Jamaica',
      label: langString('america/jamaica', 'timezones'),
    },
    {
      id: 'America/Juneau',
      label: langString('america/juneau', 'timezones'),
    },
    {
      id: 'America/Kentucky/Monticello',
      label: langString('america/kentucky/monticello', 'timezones'),
    },
    {
      id: 'America/La_Paz',
      label: langString('america/la_paz', 'timezones'),
    },
    {
      id: 'America/Lima',
      label: langString('america/lima', 'timezones'),
    },
    {
      id: 'America/Los_Angeles',
      label: langString('america/los_angeles', 'timezones'),
    },
    {
      id: 'America/Louisville',
      label: langString('america/louisville', 'timezones'),
    },
    {
      id: 'America/Maceio',
      label: langString('america/maceio', 'timezones'),
    },
    {
      id: 'America/Managua',
      label: langString('america/managua', 'timezones'),
    },
    {
      id: 'America/Manaus',
      label: langString('america/manaus', 'timezones'),
    },
    {
      id: 'America/Martinique',
      label: langString('america/martinique', 'timezones'),
    },
    {
      id: 'America/Mazatlan',
      label: langString('america/mazatlan', 'timezones'),
    },
    {
      id: 'America/Menominee',
      label: langString('america/menominee', 'timezones'),
    },
    {
      id: 'America/Merida',
      label: langString('america/merida', 'timezones'),
    },
    {
      id: 'America/Mexico_City',
      label: langString('america/mexico_city', 'timezones'),
    },
    {
      id: 'America/Miquelon',
      label: langString('america/miquelon', 'timezones'),
    },
    {
      id: 'America/Monterrey',
      label: langString('america/monterrey', 'timezones'),
    },
    {
      id: 'America/Montevideo',
      label: langString('america/montevideo', 'timezones'),
    },
    {
      id: 'America/Montreal',
      label: langString('america/montreal', 'timezones'),
    },
    {
      id: 'America/Montserrat',
      label: langString('america/montserrat', 'timezones'),
    },
    {
      id: 'America/Nassau',
      label: langString('america/nassau', 'timezones'),
    },
    {
      id: 'America/New_York',
      label: langString('america/new_york', 'timezones'),
    },
    {
      id: 'America/Nipigon',
      label: langString('america/nipigon', 'timezones'),
    },
    {
      id: 'America/Nome',
      label: langString('america/nome', 'timezones'),
    },
    {
      id: 'America/Noronha',
      label: langString('america/noronha', 'timezones'),
    },
    {
      id: 'America/North_Dakota/Center',
      label: langString('america/north_dakota/center', 'timezones'),
    },
    {
      id: 'America/Panama',
      label: langString('america/panama', 'timezones'),
    },
    {
      id: 'America/Pangnirtung',
      label: langString('america/pangnirtung', 'timezones'),
    },
    {
      id: 'America/Paramaribo',
      label: langString('america/paramaribo', 'timezones'),
    },
    {
      id: 'America/Phoenix',
      label: langString('america/phoenix', 'timezones'),
    },
    {
      id: 'America/Port_of_Spain',
      label: langString('america/port_of_spain', 'timezones'),
    },
    {
      id: 'America/Port-au-Prince',
      label: langString('america/port-au-prince', 'timezones'),
    },
    {
      id: 'America/Porto_Velho',
      label: langString('america/porto_velho', 'timezones'),
    },
    {
      id: 'America/Puerto_Rico',
      label: langString('america/puerto_rico', 'timezones'),
    },
    {
      id: 'America/Rainy_River',
      label: langString('america/rainy_river', 'timezones'),
    },
    {
      id: 'America/Rankin_Inlet',
      label: langString('america/rankin_inlet', 'timezones'),
    },
    {
      id: 'America/Recife',
      label: langString('america/recife', 'timezones'),
    },
    {
      id: 'America/Regina',
      label: langString('america/regina', 'timezones'),
    },
    {
      id: 'America/Rio_Branco',
      label: langString('america/rio_branco', 'timezones'),
    },
    {
      id: 'America/Santiago',
      label: langString('america/santiago', 'timezones'),
    },
    {
      id: 'America/Santo_Domingo',
      label: langString('america/santo_domingo', 'timezones'),
    },
    {
      id: 'America/Sao_Paulo',
      label: langString('america/sao_paulo', 'timezones'),
    },
    {
      id: 'America/Scoresbysund',
      label: langString('america/scoresbysund', 'timezones'),
    },
    {
      id: 'America/St_Johns',
      label: langString('america/st_johns', 'timezones'),
    },
    {
      id: 'America/St_Kitts',
      label: langString('america/st_kitts', 'timezones'),
    },
    {
      id: 'America/St_Lucia',
      label: langString('america/st_lucia', 'timezones'),
    },
    {
      id: 'America/St_Thomas',
      label: langString('america/st_thomas', 'timezones'),
    },
    {
      id: 'America/St_Vincent',
      label: langString('america/st_vincent', 'timezones'),
    },
    {
      id: 'America/Swift_Current',
      label: langString('america/swift_current', 'timezones'),
    },
    {
      id: 'America/Tegucigalpa',
      label: langString('america/tegucigalpa', 'timezones'),
    },
    {
      id: 'America/Thule',
      label: langString('america/thule', 'timezones'),
    },
    {
      id: 'America/Thunder_Bay',
      label: langString('america/thunder_bay', 'timezones'),
    },
    {
      id: 'America/Tijuana',
      label: langString('america/tijuana', 'timezones'),
    },
    {
      id: 'America/Toronto',
      label: langString('america/toronto', 'timezones'),
    },
    {
      id: 'America/Tortola',
      label: langString('america/tortola', 'timezones'),
    },
    {
      id: 'America/Vancouver',
      label: langString('america/vancouver', 'timezones'),
    },
    {
      id: 'America/Whitehorse',
      label: langString('america/whitehorse', 'timezones'),
    },
    {
      id: 'America/Winnipeg',
      label: langString('america/winnipeg', 'timezones'),
    },
    {
      id: 'America/Yakutat',
      label: langString('america/yakutat', 'timezones'),
    },
    {
      id: 'America/Yellowknife',
      label: langString('america/yellowknife', 'timezones'),
    },
    {
      id: 'Antarctica/Casey',
      label: langString('antarctica/casey', 'timezones'),
    },
    {
      id: 'Antarctica/Davis',
      label: langString('antarctica/davis', 'timezones'),
    },
    {
      id: 'Antarctica/DumontDUrville',
      label: langString('antarctica/dumontdurville', 'timezones'),
    },
    {
      id: 'Antarctica/Mawson',
      label: langString('antarctica/mawson', 'timezones'),
    },
    {
      id: 'Antarctica/McMurdo',
      label: langString('antarctica/mcmurdo', 'timezones'),
    },
    {
      id: 'Antarctica/Palmer',
      label: langString('antarctica/palmer', 'timezones'),
    },
    {
      id: 'Antarctica/Rothera',
      label: langString('antarctica/rothera', 'timezones'),
    },
    {
      id: 'Antarctica/Syowa',
      label: langString('antarctica/syowa', 'timezones'),
    },
    {
      id: 'Antarctica/Vostok',
      label: langString('antarctica/vostok', 'timezones'),
    },
    {
      id: 'Asia/Aden',
      label: langString('asia/aden', 'timezones'),
    },
    {
      id: 'Asia/Almaty',
      label: langString('asia/almaty', 'timezones'),
    },
    {
      id: 'Asia/Amman',
      label: langString('asia/amman', 'timezones'),
    },
    {
      id: 'Asia/Anadyr',
      label: langString('asia/anadyr', 'timezones'),
    },
    {
      id: 'Asia/Aqtau',
      label: langString('asia/aqtau', 'timezones'),
    },
    {
      id: 'Asia/Aqtobe',
      label: langString('asia/aqtobe', 'timezones'),
    },
    {
      id: 'Asia/Ashgabat',
      label: langString('asia/ashgabat', 'timezones'),
    },
    {
      id: 'Asia/Baghdad',
      label: langString('asia/baghdad', 'timezones'),
    },
    {
      id: 'Asia/Bahrain',
      label: langString('asia/bahrain', 'timezones'),
    },
    {
      id: 'Asia/Baku',
      label: langString('asia/baku', 'timezones'),
    },
    {
      id: 'Asia/Bangkok',
      label: langString('asia/bangkok', 'timezones'),
    },
    {
      id: 'Asia/Beirut',
      label: langString('asia/beirut', 'timezones'),
    },
    {
      id: 'Asia/Bishkek',
      label: langString('asia/bishkek', 'timezones'),
    },
    {
      id: 'Asia/Brunei',
      label: langString('asia/brunei', 'timezones'),
    },
    {
      id: 'Asia/Calcutta',
      label: langString('asia/calcutta', 'timezones'),
    },
    {
      id: 'Asia/Choibalsan',
      label: langString('asia/choibalsan', 'timezones'),
    },
    {
      id: 'Asia/Chongqing',
      label: langString('asia/chongqing', 'timezones'),
    },
    {
      id: 'Asia/Colombo',
      label: langString('asia/colombo', 'timezones'),
    },
    {
      id: 'Asia/Damascus',
      label: langString('asia/damascus', 'timezones'),
    },
    {
      id: 'Asia/Dhaka',
      label: langString('asia/dhaka', 'timezones'),
    },
    {
      id: 'Asia/Dili',
      label: langString('asia/dili', 'timezones'),
    },
    {
      id: 'Asia/Dubai',
      label: langString('asia/dubai', 'timezones'),
    },
    {
      id: 'Asia/Dushanbe',
      label: langString('asia/dushanbe', 'timezones'),
    },
    {
      id: 'Asia/Gaza',
      label: langString('asia/gaza', 'timezones'),
    },
    {
      id: 'Asia/Harbin',
      label: langString('asia/harbin', 'timezones'),
    },
    {
      id: 'Asia/Hong_Kong',
      label: langString('asia/hong_kong', 'timezones'),
    },
    {
      id: 'Asia/Hovd',
      label: langString('asia/hovd', 'timezones'),
    },
    {
      id: 'Asia/Irkutsk',
      label: langString('asia/irkutsk', 'timezones'),
    },
    {
      id: 'Asia/Jayapura',
      label: langString('asia/jayapura', 'timezones'),
    },
    {
      id: 'Asia/Jerusalem',
      label: langString('asia/jerusalem', 'timezones'),
    },
    {
      id: 'Asia/Kabul',
      label: langString('asia/kabul', 'timezones'),
    },
    {
      id: 'Asia/Kamchatka',
      label: langString('asia/kamchatka', 'timezones'),
    },
    {
      id: 'Asia/Karachi',
      label: langString('asia/karachi', 'timezones'),
    },
    {
      id: 'Asia/Kashgar',
      label: langString('asia/kashgar', 'timezones'),
    },
    {
      id: 'Asia/Katmandu',
      label: langString('asia/katmandu', 'timezones'),
    },
    {
      id: 'Asia/Kolkata',
      label: langString('asia/kolkata', 'timezones'),
    },
    {
      id: 'Asia/Krasnoyarsk',
      label: langString('asia/krasnoyarsk', 'timezones'),
    },
    {
      id: 'Asia/Kuala_Lumpur',
      label: langString('asia/kuala_lumpur', 'timezones'),
    },
    {
      id: 'Asia/Kuching',
      label: langString('asia/kuching', 'timezones'),
    },
    {
      id: 'Asia/Kuwait',
      label: langString('asia/kuwait', 'timezones'),
    },
    {
      id: 'Asia/Macau',
      label: langString('asia/macau', 'timezones'),
    },
    {
      id: 'Asia/Magadan',
      label: langString('asia/magadan', 'timezones'),
    },
    {
      id: 'Asia/Makassar',
      label: langString('asia/makassar', 'timezones'),
    },
    {
      id: 'Asia/Manila',
      label: langString('asia/manila', 'timezones'),
    },
    {
      id: 'Asia/Muscat',
      label: langString('asia/muscat', 'timezones'),
    },
    {
      id: 'Asia/Nicosia',
      label: langString('asia/nicosia', 'timezones'),
    },
    {
      id: 'Asia/Novosibirsk',
      label: langString('asia/novosibirsk', 'timezones'),
    },
    {
      id: 'Asia/Omsk',
      label: langString('asia/omsk', 'timezones'),
    },
    {
      id: 'Asia/Oral',
      label: langString('asia/oral', 'timezones'),
    },
    {
      id: 'Asia/Phnom_Penh',
      label: langString('asia/phnom_penh', 'timezones'),
    },
    {
      id: 'Asia/Pontianak',
      label: langString('asia/pontianak', 'timezones'),
    },
    {
      id: 'Asia/Pyongyang',
      label: langString('asia/pyongyang', 'timezones'),
    },
    {
      id: 'Asia/Qatar',
      label: langString('asia/qatar', 'timezones'),
    },
    {
      id: 'Asia/Qyzylorda',
      label: langString('asia/qyzylorda', 'timezones'),
    },
    {
      id: 'Asia/Rangoon',
      label: langString('asia/rangoon', 'timezones'),
    },
    {
      id: 'Asia/Riyadh',
      label: langString('asia/riyadh', 'timezones'),
    },
    {
      id: 'Asia/Saigon',
      label: langString('asia/saigon', 'timezones'),
    },
    {
      id: 'Asia/Sakhalin',
      label: langString('asia/sakhalin', 'timezones'),
    },
    {
      id: 'Asia/Samarkand',
      label: langString('asia/samarkand', 'timezones'),
    },
    {
      id: 'Asia/Seoul',
      label: langString('asia/seoul', 'timezones'),
    },
    {
      id: 'Asia/Shanghai',
      label: langString('asia/shanghai', 'timezones'),
    },
    {
      id: 'Asia/Singapore',
      label: langString('asia/singapore', 'timezones'),
    },
    {
      id: 'Asia/Taipei',
      label: langString('asia/taipei', 'timezones'),
    },
    {
      id: 'Asia/Tashkent',
      label: langString('asia/tashkent', 'timezones'),
    },
    {
      id: 'Asia/Tbilisi',
      label: langString('asia/tbilisi', 'timezones'),
    },
    {
      id: 'Asia/Tehran',
      label: langString('asia/tehran', 'timezones'),
    },
    {
      id: 'Asia/Thimphu',
      label: langString('asia/thimphu', 'timezones'),
    },
    {
      id: 'Asia/Tokyo',
      label: langString('asia/tokyo', 'timezones'),
    },
    {
      id: 'Asia/Ulaanbaatar',
      label: langString('asia/ulaanbaatar', 'timezones'),
    },
    {
      id: 'Asia/Urumqi',
      label: langString('asia/urumqi', 'timezones'),
    },
    {
      id: 'Asia/Vientiane',
      label: langString('asia/vientiane', 'timezones'),
    },
    {
      id: 'Asia/Vladivostok',
      label: langString('asia/vladivostok', 'timezones'),
    },
    {
      id: 'Asia/Yakutsk',
      label: langString('asia/yakutsk', 'timezones'),
    },
    {
      id: 'Asia/Yekaterinburg',
      label: langString('asia/yekaterinburg', 'timezones'),
    },
    {
      id: 'Asia/Yerevan',
      label: langString('asia/yerevan', 'timezones'),
    },
    {
      id: 'Atlantic/Azores',
      label: langString('atlantic/azores', 'timezones'),
    },
    {
      id: 'Atlantic/Bermuda',
      label: langString('atlantic/bermuda', 'timezones'),
    },
    {
      id: 'Atlantic/Canary',
      label: langString('atlantic/canary', 'timezones'),
    },
    {
      id: 'Atlantic/Cape_Verde',
      label: langString('atlantic/cape_verde', 'timezones'),
    },
    {
      id: 'Atlantic/Faeroe',
      label: langString('atlantic/faeroe', 'timezones'),
    },
    {
      id: 'Atlantic/Madeira',
      label: langString('atlantic/madeira', 'timezones'),
    },
    {
      id: 'Atlantic/Reykjavik',
      label: langString('atlantic/reykjavik', 'timezones'),
    },
    {
      id: 'Atlantic/South_Georgia',
      label: langString('atlantic/south_georgia', 'timezones'),
    },
    {
      id: 'Atlantic/St_Helena',
      label: langString('atlantic/st_helena', 'timezones'),
    },
    {
      id: 'Atlantic/Stanley',
      label: langString('atlantic/stanley', 'timezones'),
    },
    {
      id: 'Australia/Adelaide',
      label: langString('australia/adelaide', 'timezones'),
    },
    {
      id: 'Australia/Brisbane',
      label: langString('australia/brisbane', 'timezones'),
    },
    {
      id: 'Australia/Broken_Hill',
      label: langString('australia/broken_hill', 'timezones'),
    },
    {
      id: 'Australia/Darwin',
      label: langString('australia/darwin', 'timezones'),
    },
    {
      id: 'Australia/Hobart',
      label: langString('australia/hobart', 'timezones'),
    },
    {
      id: 'Australia/Lindeman',
      label: langString('australia/lindeman', 'timezones'),
    },
    {
      id: 'Australia/Lord_Howe',
      label: langString('australia/lord_howe', 'timezones'),
    },
    {
      id: 'Australia/Melbourne',
      label: langString('australia/melbourne', 'timezones'),
    },
    {
      id: 'Australia/Perth',
      label: langString('australia/perth', 'timezones'),
    },
    {
      id: 'Australia/Sydney',
      label: langString('australia/sydney', 'timezones'),
    },
    {
      id: 'Europe/Amsterdam',
      label: langString('europe/amsterdam', 'timezones'),
    },
    {
      id: 'Europe/Andorra',
      label: langString('europe/andorra', 'timezones'),
    },
    {
      id: 'Europe/Athens',
      label: langString('europe/athens', 'timezones'),
    },
    {
      id: 'Europe/Belfast',
      label: langString('europe/belfast', 'timezones'),
    },
    {
      id: 'Europe/Belgrade',
      label: langString('europe/belgrade', 'timezones'),
    },
    {
      id: 'Europe/Berlin',
      label: langString('europe/berlin', 'timezones'),
    },
    {
      id: 'Europe/Brussels',
      label: langString('europe/brussels', 'timezones'),
    },
    {
      id: 'Europe/Bucharest',
      label: langString('europe/bucharest', 'timezones'),
    },
    {
      id: 'Europe/Budapest',
      label: langString('europe/budapest', 'timezones'),
    },
    {
      id: 'Europe/Chisinau',
      label: langString('europe/chisinau', 'timezones'),
    },
    {
      id: 'Europe/Copenhagen',
      label: langString('europe/copenhagen', 'timezones'),
    },
    {
      id: 'Europe/Dublin',
      label: langString('europe/dublin', 'timezones'),
    },
    {
      id: 'Europe/Gibraltar',
      label: langString('europe/gibraltar', 'timezones'),
    },
    {
      id: 'Europe/Helsinki',
      label: langString('europe/helsinki', 'timezones'),
    },
    {
      id: 'Europe/Istanbul',
      label: langString('europe/istanbul', 'timezones'),
    },
    {
      id: 'Europe/Kaliningrad',
      label: langString('europe/kaliningrad', 'timezones'),
    },
    {
      id: 'Europe/Kiev',
      label: langString('europe/kiev', 'timezones'),
    },
    {
      id: 'Europe/Lisbon',
      label: langString('europe/lisbon', 'timezones'),
    },
    {
      id: 'Europe/London',
      label: langString('europe/london', 'timezones'),
    },
    {
      id: 'Europe/Luxembourg',
      label: langString('europe/luxembourg', 'timezones'),
    },
    {
      id: 'Europe/Madrid',
      label: langString('europe/madrid', 'timezones'),
    },
    {
      id: 'Europe/Malta',
      label: langString('europe/malta', 'timezones'),
    },
    {
      id: 'Europe/Minsk',
      label: langString('europe/minsk', 'timezones'),
    },
    {
      id: 'Europe/Monaco',
      label: langString('europe/monaco', 'timezones'),
    },
    {
      id: 'Europe/Moscow',
      label: langString('europe/moscow', 'timezones'),
    },
    {
      id: 'Europe/Oslo',
      label: langString('europe/oslo', 'timezones'),
    },
    {
      id: 'Europe/Paris',
      label: langString('europe/paris', 'timezones'),
    },
    {
      id: 'Europe/Prague',
      label: langString('europe/prague', 'timezones'),
    },
    {
      id: 'Europe/Riga',
      label: langString('europe/riga', 'timezones'),
    },
    {
      id: 'Europe/Rome',
      label: langString('europe/rome', 'timezones'),
    },
    {
      id: 'Europe/Samara',
      label: langString('europe/samara', 'timezones'),
    },
    {
      id: 'Europe/Simferopol',
      label: langString('europe/simferopol', 'timezones'),
    },
    {
      id: 'Europe/Sofia',
      label: langString('europe/sofia', 'timezones'),
    },
    {
      id: 'Europe/Stockholm',
      label: langString('europe/stockholm', 'timezones'),
    },
    {
      id: 'Europe/Tallinn',
      label: langString('europe/tallinn', 'timezones'),
    },
    {
      id: 'Europe/Tirane',
      label: langString('europe/tirane', 'timezones'),
    },
    {
      id: 'Europe/Uzhgorod',
      label: langString('europe/uzhgorod', 'timezones'),
    },
    {
      id: 'Europe/Vaduz',
      label: langString('europe/vaduz', 'timezones'),
    },
    {
      id: 'Europe/Vienna',
      label: langString('europe/vienna', 'timezones'),
    },
    {
      id: 'Europe/Vilnius',
      label: langString('europe/vilnius', 'timezones'),
    },
    {
      id: 'Europe/Warsaw',
      label: langString('europe/warsaw', 'timezones'),
    },
    {
      id: 'Europe/Zaporozhye',
      label: langString('europe/zaporozhye', 'timezones'),
    },
    {
      id: 'Europe/Zurich',
      label: langString('europe/zurich', 'timezones'),
    },
    {
      id: 'Indian/Antananarivo',
      label: langString('indian/antananarivo', 'timezones'),
    },
    {
      id: 'Indian/Chagos',
      label: langString('indian/chagos', 'timezones'),
    },
    {
      id: 'Indian/Christmas',
      label: langString('indian/christmas', 'timezones'),
    },
    {
      id: 'Indian/Comoro',
      label: langString('indian/comoro', 'timezones'),
    },
    {
      id: 'Indian/Kerguelen',
      label: langString('indian/kerguelen', 'timezones'),
    },
    {
      id: 'Indian/Mahe',
      label: langString('indian/mahe', 'timezones'),
    },
    {
      id: 'Indian/Maldives',
      label: langString('indian/maldives', 'timezones'),
    },
    {
      id: 'Indian/Mauritius',
      label: langString('indian/mauritius', 'timezones'),
    },
    {
      id: 'Indian/Mayotte',
      label: langString('indian/mayotte', 'timezones'),
    },
    {
      id: 'Indian/Reunion',
      label: langString('indian/reunion', 'timezones'),
    },
    {
      id: 'Pacific/Apia',
      label: langString('pacific/apia', 'timezones'),
    },
    {
      id: 'Pacific/Auckland',
      label: langString('pacific/auckland', 'timezones'),
    },
    {
      id: 'Pacific/Chatham',
      label: langString('pacific/chatham', 'timezones'),
    },
    {
      id: 'Pacific/Easter',
      label: langString('pacific/easter', 'timezones'),
    },
    {
      id: 'Pacific/Efate',
      label: langString('pacific/efate', 'timezones'),
    },
    {
      id: 'Pacific/Enderbury',
      label: langString('pacific/enderbury', 'timezones'),
    },
    {
      id: 'Pacific/Fakaofo',
      label: langString('pacific/fakaofo', 'timezones'),
    },
    {
      id: 'Pacific/Fiji',
      label: langString('pacific/fiji', 'timezones'),
    },
    {
      id: 'Pacific/Funafuti',
      label: langString('pacific/funafuti', 'timezones'),
    },
    {
      id: 'Pacific/Galapagos',
      label: langString('pacific/galapagos', 'timezones'),
    },
    {
      id: 'Pacific/Gambier',
      label: langString('pacific/gambier', 'timezones'),
    },
    {
      id: 'Pacific/Guadalcanal',
      label: langString('pacific/guadalcanal', 'timezones'),
    },
    {
      id: 'Pacific/Guam',
      label: langString('pacific/guam', 'timezones'),
    },
    {
      id: 'Pacific/Honolulu',
      label: langString('pacific/honolulu', 'timezones'),
    },
    {
      id: 'Pacific/Kiritimati',
      label: langString('pacific/kiritimati', 'timezones'),
    },
    {
      id: 'Pacific/Kosrae',
      label: langString('pacific/kosrae', 'timezones'),
    },
    {
      id: 'Pacific/Kwajalein',
      label: langString('pacific/kwajalein', 'timezones'),
    },
    {
      id: 'Pacific/Majuro',
      label: langString('pacific/majuro', 'timezones'),
    },
    {
      id: 'Pacific/Marquesas',
      label: langString('pacific/marquesas', 'timezones'),
    },
    {
      id: 'Pacific/Midway',
      label: langString('pacific/midway', 'timezones'),
    },
    {
      id: 'Pacific/Nauru',
      label: langString('pacific/nauru', 'timezones'),
    },
    {
      id: 'Pacific/Niue',
      label: langString('pacific/niue', 'timezones'),
    },
    {
      id: 'Pacific/Norfolk',
      label: langString('pacific/norfolk', 'timezones'),
    },
    {
      id: 'Pacific/Noumea',
      label: langString('pacific/noumea', 'timezones'),
    },
    {
      id: 'Pacific/Pago_Pago',
      label: langString('pacific/pago_pago', 'timezones'),
    },
    {
      id: 'Pacific/Palau',
      label: langString('pacific/palau', 'timezones'),
    },
    {
      id: 'Pacific/Pitcairn',
      label: langString('pacific/pitcairn', 'timezones'),
    },
    {
      id: 'Pacific/Ponape',
      label: langString('pacific/ponape', 'timezones'),
    },
    {
      id: 'Pacific/Port_Moresby',
      label: langString('pacific/port_moresby', 'timezones'),
    },
    {
      id: 'Pacific/Rarotonga',
      label: langString('pacific/rarotonga', 'timezones'),
    },
    {
      id: 'Pacific/Saipan',
      label: langString('pacific/saipan', 'timezones'),
    },
    {
      id: 'Pacific/Tahiti',
      label: langString('pacific/tahiti', 'timezones'),
    },
    {
      id: 'Pacific/Tarawa',
      label: langString('pacific/tarawa', 'timezones'),
    },
    {
      id: 'Pacific/Tongatapu',
      label: langString('pacific/tongatapu', 'timezones'),
    },
    {
      id: 'Pacific/Truk',
      label: langString('pacific/truk', 'timezones'),
    },
    {
      id: 'Pacific/Wake',
      label: langString('pacific/wake', 'timezones'),
    },
    {
      id: 'Pacific/Wallis',
      label: langString('pacific/wallis', 'timezones'),
    },
    {
      id: 'Pacific/Yap',
      label: langString('pacific/yap', 'timezones'),
    },
    {
      id: 'UTC',
      label: langString('utc', 'timezones'),
    },
  ];
  return zones;
}
