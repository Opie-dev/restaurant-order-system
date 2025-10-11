<?php

namespace App\Constants;

/**
 * Country Codes based on ITU-T E.164 international numbering plan
 * Source: International Telecommunication Union (ITU)
 * Last Updated: 2024
 * 
 * Note: This list includes officially registered country codes with ITU
 * Some codes are shared between countries (e.g., +1 for North America)
 * Some codes are reserved for future use
 */
class CountryCodes
{
    /**
     * Default regex pattern for phone numbers
     * Minimum 6 digits
     */
    private const DEFAULT_REGEX = '/^\d{6,}$/';

    /**
     * Get all country codes with their details
     * All codes are officially registered with ITU
     *
     * @return array
     */
    public static function getAll(): array
    {
        $defaultRegex = '/^\d{6,}$/'; // Minimum 6 digits

        return [
            // Zone 1: North America (+1)
            [
                'code' => '+1',
                'label' => 'US/CA (+1)',
                'country' => 'United States/Canada',
                'regex' => '/^[2-9]\d{9}$/' // 10 digits, first digit 2-9
            ],
            [
                'code' => '+1242',
                'label' => 'BS (+1242)',
                'country' => 'Bahamas',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1246',
                'label' => 'BB (+1246)',
                'country' => 'Barbados',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1264',
                'label' => 'AI (+1264)',
                'country' => 'Anguilla',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1268',
                'label' => 'AG (+1268)',
                'country' => 'Antigua and Barbuda',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1284',
                'label' => 'VG (+1284)',
                'country' => 'British Virgin Islands',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1340',
                'label' => 'VI (+1340)',
                'country' => 'US Virgin Islands',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1345',
                'label' => 'KY (+1345)',
                'country' => 'Cayman Islands',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1441',
                'label' => 'BM (+1441)',
                'country' => 'Bermuda',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1473',
                'label' => 'GD (+1473)',
                'country' => 'Grenada',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1649',
                'label' => 'TC (+1649)',
                'country' => 'Turks and Caicos Islands',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1664',
                'label' => 'MS (+1664)',
                'country' => 'Montserrat',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1670',
                'label' => 'MP (+1670)',
                'country' => 'Northern Mariana Islands',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1671',
                'label' => 'GU (+1671)',
                'country' => 'Guam',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1758',
                'label' => 'LC (+1758)',
                'country' => 'Saint Lucia',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1767',
                'label' => 'DM (+1767)',
                'country' => 'Dominica',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1784',
                'label' => 'VC (+1784)',
                'country' => 'Saint Vincent and the Grenadines',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1787',
                'label' => 'PR (+1787)',
                'country' => 'Puerto Rico',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1809',
                'label' => 'DO (+1809)',
                'country' => 'Dominican Republic',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+1868',
                'label' => 'TT (+1868)',
                'country' => 'Trinidad and Tobago',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1869',
                'label' => 'KN (+1869)',
                'country' => 'Saint Kitts and Nevis',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+1876',
                'label' => 'JM (+1876)',
                'country' => 'Jamaica',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],

            // Zone 2: Africa
            [
                'code' => '+20',
                'label' => 'EG (+20)',
                'country' => 'Egypt',
                'regex' => '/^[1-9]\d{9}$/' // 10 digits, first digit 1-9
            ],
            [
                'code' => '+27',
                'label' => 'ZA (+27)',
                'country' => 'South Africa',
                'regex' => '/^[1-9]\d{8}$/' // 9 digits, first digit 1-9
            ],
            [
                'code' => '+212',
                'label' => 'MA (+212)',
                'country' => 'Morocco',
                'regex' => '/^[5-9]\d{8}$/' // 9 digits, first digit 5-9
            ],
            [
                'code' => '+213',
                'label' => 'DZ (+213)',
                'country' => 'Algeria',
                'regex' => '/^[5-9]\d{8}$/' // 9 digits, first digit 5-9
            ],
            [
                'code' => '+216',
                'label' => 'TN (+216)',
                'country' => 'Tunisia',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+218',
                'label' => 'LY (+218)',
                'country' => 'Libya',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+220',
                'label' => 'GM (+220)',
                'country' => 'Gambia',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+221',
                'label' => 'SN (+221)',
                'country' => 'Senegal',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+222',
                'label' => 'MR (+222)',
                'country' => 'Mauritania',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+223',
                'label' => 'ML (+223)',
                'country' => 'Mali',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+224',
                'label' => 'GN (+224)',
                'country' => 'Guinea',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+225',
                'label' => 'CI (+225)',
                'country' => 'Ivory Coast',
                'regex' => '/^[0-9]\d{9}$/' // 10 digits, first digit 0-9
            ],
            [
                'code' => '+226',
                'label' => 'BF (+226)',
                'country' => 'Burkina Faso',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+227',
                'label' => 'NE (+227)',
                'country' => 'Niger',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+228',
                'label' => 'TG (+228)',
                'country' => 'Togo',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+229',
                'label' => 'BJ (+229)',
                'country' => 'Benin',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+230',
                'label' => 'MU (+230)',
                'country' => 'Mauritius',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+231',
                'label' => 'LR (+231)',
                'country' => 'Liberia',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+232',
                'label' => 'SL (+232)',
                'country' => 'Sierra Leone',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+233',
                'label' => 'GH (+233)',
                'country' => 'Ghana',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+234',
                'label' => 'NG (+234)',
                'country' => 'Nigeria',
                'regex' => '/^[2-9]\d{9}$/' // 10 digits, first digit 2-9
            ],
            [
                'code' => '+235',
                'label' => 'TD (+235)',
                'country' => 'Chad',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+236',
                'label' => 'CF (+236)',
                'country' => 'Central African Republic',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+237',
                'label' => 'CM (+237)',
                'country' => 'Cameroon',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+238',
                'label' => 'CV (+238)',
                'country' => 'Cape Verde',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+239',
                'label' => 'ST (+239)',
                'country' => 'São Tomé and Príncipe',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+240',
                'label' => 'GQ (+240)',
                'country' => 'Equatorial Guinea',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+241',
                'label' => 'GA (+241)',
                'country' => 'Gabon',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+242',
                'label' => 'CG (+242)',
                'country' => 'Republic of the Congo',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+243',
                'label' => 'CD (+243)',
                'country' => 'Democratic Republic of the Congo',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+244',
                'label' => 'AO (+244)',
                'country' => 'Angola',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+245',
                'label' => 'GW (+245)',
                'country' => 'Guinea-Bissau',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+246',
                'label' => 'IO (+246)',
                'country' => 'British Indian Ocean Territory',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],
            [
                'code' => '+248',
                'label' => 'SC (+248)',
                'country' => 'Seychelles',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+249',
                'label' => 'SD (+249)',
                'country' => 'Sudan',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+250',
                'label' => 'RW (+250)',
                'country' => 'Rwanda',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+251',
                'label' => 'ET (+251)',
                'country' => 'Ethiopia',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+252',
                'label' => 'SO (+252)',
                'country' => 'Somalia',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+253',
                'label' => 'DJ (+253)',
                'country' => 'Djibouti',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+254',
                'label' => 'KE (+254)',
                'country' => 'Kenya',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+255',
                'label' => 'TZ (+255)',
                'country' => 'Tanzania',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+256',
                'label' => 'UG (+256)',
                'country' => 'Uganda',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+257',
                'label' => 'BI (+257)',
                'country' => 'Burundi',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+258',
                'label' => 'MZ (+258)',
                'country' => 'Mozambique',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+260',
                'label' => 'ZM (+260)',
                'country' => 'Zambia',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+261',
                'label' => 'MG (+261)',
                'country' => 'Madagascar',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+262',
                'label' => 'RE (+262)',
                'country' => 'Réunion',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+263',
                'label' => 'ZW (+263)',
                'country' => 'Zimbabwe',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+264',
                'label' => 'NA (+264)',
                'country' => 'Namibia',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+265',
                'label' => 'MW (+265)',
                'country' => 'Malawi',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+266',
                'label' => 'LS (+266)',
                'country' => 'Lesotho',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+267',
                'label' => 'BW (+267)',
                'country' => 'Botswana',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+268',
                'label' => 'SZ (+268)',
                'country' => 'Eswatini',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+269',
                'label' => 'KM (+269)',
                'country' => 'Comoros',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+290',
                'label' => 'SH (+290)',
                'country' => 'Saint Helena',
                'regex' => '/^[2-9]\d{4}$/' // 5 digits, first digit 2-9
            ],
            [
                'code' => '+291',
                'label' => 'ER (+291)',
                'country' => 'Eritrea',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+297',
                'label' => 'AW (+297)',
                'country' => 'Aruba',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+298',
                'label' => 'FO (+298)',
                'country' => 'Faroe Islands',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],
            [
                'code' => '+299',
                'label' => 'GL (+299)',
                'country' => 'Greenland',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],

            // Zone 3: Europe
            [
                'code' => '+30',
                'label' => 'GR (+30)',
                'country' => 'Greece',
                'regex' => '/^[2-9]\d{9}$/' // 10 digits, first digit 2-9
            ],
            [
                'code' => '+31',
                'label' => 'NL (+31)',
                'country' => 'Netherlands',
                'regex' => '/^[1-9]\d{8}$/' // 9 digits, first digit 1-9
            ],
            [
                'code' => '+32',
                'label' => 'BE (+32)',
                'country' => 'Belgium',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+33',
                'label' => 'FR (+33)',
                'country' => 'France',
                'regex' => '/^[1-9]\d{8}$/' // 9 digits, first digit 1-9
            ],
            [
                'code' => '+34',
                'label' => 'ES (+34)',
                'country' => 'Spain',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+36',
                'label' => 'HU (+36)',
                'country' => 'Hungary',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+39',
                'label' => 'IT (+39)',
                'country' => 'Italy',
                'regex' => '/^[2-9]\d{8,9}$/' // 9-10 digits, first digit 2-9
            ],
            [
                'code' => '+40',
                'label' => 'RO (+40)',
                'country' => 'Romania',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+41',
                'label' => 'CH (+41)',
                'country' => 'Switzerland',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+43',
                'label' => 'AT (+43)',
                'country' => 'Austria',
                'regex' => '/^[1-9]\d{9,10}$/' // 10-11 digits, first digit 1-9
            ],
            [
                'code' => '+44',
                'label' => 'GB (+44)',
                'country' => 'United Kingdom',
                'regex' => '/^[2-9]\d{9}$/' // 10 digits, first digit 2-9
            ],
            [
                'code' => '+45',
                'label' => 'DK (+45)',
                'country' => 'Denmark',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+46',
                'label' => 'SE (+46)',
                'country' => 'Sweden',
                'regex' => '/^[1-9]\d{8}$/' // 9 digits, first digit 1-9
            ],
            [
                'code' => '+47',
                'label' => 'NO (+47)',
                'country' => 'Norway',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+48',
                'label' => 'PL (+48)',
                'country' => 'Poland',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+49',
                'label' => 'DE (+49)',
                'country' => 'Germany',
                'regex' => '/^[2-9]\d{8,9}$/' // 9-10 digits, first digit 2-9
            ],

            // Zone 4: Europe
            [
                'code' => '+350',
                'label' => 'GI (+350)',
                'country' => 'Gibraltar',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+351',
                'label' => 'PT (+351)',
                'country' => 'Portugal',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+352',
                'label' => 'LU (+352)',
                'country' => 'Luxembourg',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+353',
                'label' => 'IE (+353)',
                'country' => 'Ireland',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+354',
                'label' => 'IS (+354)',
                'country' => 'Iceland',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+355',
                'label' => 'AL (+355)',
                'country' => 'Albania',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+356',
                'label' => 'MT (+356)',
                'country' => 'Malta',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+357',
                'label' => 'CY (+357)',
                'country' => 'Cyprus',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+358',
                'label' => 'FI (+358)',
                'country' => 'Finland',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+359',
                'label' => 'BG (+359)',
                'country' => 'Bulgaria',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+370',
                'label' => 'LT (+370)',
                'country' => 'Lithuania',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+371',
                'label' => 'LV (+371)',
                'country' => 'Latvia',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+372',
                'label' => 'EE (+372)',
                'country' => 'Estonia',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+373',
                'label' => 'MD (+373)',
                'country' => 'Moldova',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+374',
                'label' => 'AM (+374)',
                'country' => 'Armenia',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+375',
                'label' => 'BY (+375)',
                'country' => 'Belarus',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+376',
                'label' => 'AD (+376)',
                'country' => 'Andorra',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],
            [
                'code' => '+377',
                'label' => 'MC (+377)',
                'country' => 'Monaco',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+378',
                'label' => 'SM (+378)',
                'country' => 'San Marino',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+379',
                'label' => 'VA (+379)',
                'country' => 'Vatican City',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+380',
                'label' => 'UA (+380)',
                'country' => 'Ukraine',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+381',
                'label' => 'RS (+381)',
                'country' => 'Serbia',
                'regex' => '/^[2-9]\d{7,8}$/' // 8-9 digits, first digit 2-9
            ],
            [
                'code' => '+382',
                'label' => 'ME (+382)',
                'country' => 'Montenegro',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+383',
                'label' => 'XK (+383)',
                'country' => 'Kosovo',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+385',
                'label' => 'HR (+385)',
                'country' => 'Croatia',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+386',
                'label' => 'SI (+386)',
                'country' => 'Slovenia',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+387',
                'label' => 'BA (+387)',
                'country' => 'Bosnia and Herzegovina',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+389',
                'label' => 'MK (+389)',
                'country' => 'North Macedonia',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],

            // Zone 5: South America
            [
                'code' => '+500',
                'label' => 'FK (+500)',
                'country' => 'Falkland Islands',
                'regex' => '/^[2-9]\d{4}$/' // 5 digits, first digit 2-9
            ],
            [
                'code' => '+501',
                'label' => 'BZ (+501)',
                'country' => 'Belize',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+502',
                'label' => 'GT (+502)',
                'country' => 'Guatemala',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+503',
                'label' => 'SV (+503)',
                'country' => 'El Salvador',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+504',
                'label' => 'HN (+504)',
                'country' => 'Honduras',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+505',
                'label' => 'NI (+505)',
                'country' => 'Nicaragua',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+506',
                'label' => 'CR (+506)',
                'country' => 'Costa Rica',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+507',
                'label' => 'PA (+507)',
                'country' => 'Panama',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+508',
                'label' => 'PM (+508)',
                'country' => 'Saint Pierre and Miquelon',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],
            [
                'code' => '+509',
                'label' => 'HT (+509)',
                'country' => 'Haiti',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+51',
                'label' => 'PE (+51)',
                'country' => 'Peru',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+52',
                'label' => 'MX (+52)',
                'country' => 'Mexico',
                'regex' => '/^[2-9]\d{9}$/' // 10 digits, first digit 2-9
            ],
            [
                'code' => '+53',
                'label' => 'CU (+53)',
                'country' => 'Cuba',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+54',
                'label' => 'AR (+54)',
                'country' => 'Argentina',
                'regex' => '/^[2-9]\d{9,10}$/' // 10-11 digits, first digit 2-9
            ],
            [
                'code' => '+55',
                'label' => 'BR (+55)',
                'country' => 'Brazil',
                'regex' => '/^[2-9]\d{10}$/' // 11 digits, first digit 2-9
            ],
            [
                'code' => '+56',
                'label' => 'CL (+56)',
                'country' => 'Chile',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+57',
                'label' => 'CO (+57)',
                'country' => 'Colombia',
                'regex' => '/^[2-9]\d{9}$/' // 10 digits, first digit 2-9
            ],
            [
                'code' => '+58',
                'label' => 'VE (+58)',
                'country' => 'Venezuela',
                'regex' => '/^[2-9]\d{9}$/' // 10 digits, first digit 2-9
            ],
            [
                'code' => '+590',
                'label' => 'GP (+590)',
                'country' => 'Guadeloupe',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+591',
                'label' => 'BO (+591)',
                'country' => 'Bolivia',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+592',
                'label' => 'GY (+592)',
                'country' => 'Guyana',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+593',
                'label' => 'EC (+593)',
                'country' => 'Ecuador',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+594',
                'label' => 'GF (+594)',
                'country' => 'French Guiana',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+595',
                'label' => 'PY (+595)',
                'country' => 'Paraguay',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+596',
                'label' => 'MQ (+596)',
                'country' => 'Martinique',
                'regex' => '/^[2-9]\d{8}$/' // 9 digits, first digit 2-9
            ],
            [
                'code' => '+597',
                'label' => 'SR (+597)',
                'country' => 'Suriname',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+598',
                'label' => 'UY (+598)',
                'country' => 'Uruguay',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+599',
                'label' => 'CW (+599)',
                'country' => 'Curaçao',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],

            // Zone 6: Southeast Asia and Oceania
            [
                'code' => '+60',
                'label' => 'MY (+60)',
                'country' => 'Malaysia',
                'regex' => '/^1\d{8,9}$/' // Starts with 1, followed by 8-9 digits
            ],
            [
                'code' => '+61',
                'label' => 'AU (+61)',
                'country' => 'Australia',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+62',
                'label' => 'ID (+62)',
                'country' => 'Indonesia',
                'regex' => '/^[2-9]\d{7,10}$/' // 8-11 digits, first digit 2-9
            ],
            [
                'code' => '+63',
                'label' => 'PH (+63)',
                'country' => 'Philippines',
                'regex' => '/^[2-9]\d{9}$/' // 10 digits, first digit 2-9
            ],
            [
                'code' => '+64',
                'label' => 'NZ (+64)',
                'country' => 'New Zealand',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+65',
                'label' => 'SG (+65)',
                'country' => 'Singapore',
                'regex' => '/^[89]\d{7}$/' // 8 digits, starts with 8 or 9
            ],
            [
                'code' => '+66',
                'label' => 'TH (+66)',
                'country' => 'Thailand',
                'regex' => '/^[1-9]\d{8}$/' // 9 digits, first digit 1-9
            ],
            [
                'code' => '+670',
                'label' => 'TL (+670)',
                'country' => 'Timor-Leste',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+672',
                'label' => 'NF (+672)',
                'country' => 'Norfolk Island',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],
            [
                'code' => '+673',
                'label' => 'BN (+673)',
                'country' => 'Brunei',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+674',
                'label' => 'NR (+674)',
                'country' => 'Nauru',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],
            [
                'code' => '+675',
                'label' => 'PG (+675)',
                'country' => 'Papua New Guinea',
                'regex' => '/^[2-9]\d{7}$/' // 8 digits, first digit 2-9
            ],
            [
                'code' => '+676',
                'label' => 'TO (+676)',
                'country' => 'Tonga',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],
            [
                'code' => '+677',
                'label' => 'SB (+677)',
                'country' => 'Solomon Islands',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+678',
                'label' => 'VU (+678)',
                'country' => 'Vanuatu',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+679',
                'label' => 'FJ (+679)',
                'country' => 'Fiji',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+680',
                'label' => 'PW (+680)',
                'country' => 'Palau',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+681',
                'label' => 'WF (+681)',
                'country' => 'Wallis and Futuna',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],
            [
                'code' => '+682',
                'label' => 'CK (+682)',
                'country' => 'Cook Islands',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],
            [
                'code' => '+683',
                'label' => 'NU (+683)',
                'country' => 'Niue',
                'regex' => '/^[2-9]\d{4}$/' // 5 digits, first digit 2-9
            ],
            [
                'code' => '+685',
                'label' => 'WS (+685)',
                'country' => 'Samoa',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+686',
                'label' => 'KI (+686)',
                'country' => 'Kiribati',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+687',
                'label' => 'NC (+687)',
                'country' => 'New Caledonia',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+688',
                'label' => 'TV (+688)',
                'country' => 'Tuvalu',
                'regex' => '/^[2-9]\d{5}$/' // 6 digits, first digit 2-9
            ],
            [
                'code' => '+689',
                'label' => 'PF (+689)',
                'country' => 'French Polynesia',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+690',
                'label' => 'TK (+690)',
                'country' => 'Tokelau',
                'regex' => '/^[2-9]\d{4}$/' // 5 digits, first digit 2-9
            ],
            [
                'code' => '+691',
                'label' => 'FM (+691)',
                'country' => 'Micronesia',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],
            [
                'code' => '+692',
                'label' => 'MH (+692)',
                'country' => 'Marshall Islands',
                'regex' => '/^[2-9]\d{6}$/' // 7 digits, first digit 2-9
            ],

            // Zone 7: Russia and Kazakhstan
            [
                'code' => '+7',
                'label' => 'RU/KZ (+7)',
                'country' => 'Russia/Kazakhstan',
                'regex' => $defaultRegex
            ],

            // Zone 8: East Asia
            [
                'code' => '+81',
                'label' => 'JP (+81)',
                'country' => 'Japan',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+82',
                'label' => 'KR (+82)',
                'country' => 'South Korea',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+84',
                'label' => 'VN (+84)',
                'country' => 'Vietnam',
                'regex' => '/^[3-9]\d{8}$/' // 9 digits, first digit 3-9
            ],
            [
                'code' => '+850',
                'label' => 'KP (+850)',
                'country' => 'North Korea',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+852',
                'label' => 'HK (+852)',
                'country' => 'Hong Kong',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+853',
                'label' => 'MO (+853)',
                'country' => 'Macau',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+855',
                'label' => 'KH (+855)',
                'country' => 'Cambodia',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+856',
                'label' => 'LA (+856)',
                'country' => 'Laos',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+86',
                'label' => 'CN (+86)',
                'country' => 'China',
                'regex' => '/^1[3-9]\d{9}$/' // 11 digits, starts with 1, second digit 3-9
            ],
            [
                'code' => '+880',
                'label' => 'BD (+880)',
                'country' => 'Bangladesh',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+886',
                'label' => 'TW (+886)',
                'country' => 'Taiwan',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+90',
                'label' => 'TR (+90)',
                'country' => 'Turkey',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+91',
                'label' => 'IN (+91)',
                'country' => 'India',
                'regex' => '/^[6-9]\d{9}$/' // 10 digits, first digit 6-9
            ],
            [
                'code' => '+92',
                'label' => 'PK (+92)',
                'country' => 'Pakistan',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+93',
                'label' => 'AF (+93)',
                'country' => 'Afghanistan',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+94',
                'label' => 'LK (+94)',
                'country' => 'Sri Lanka',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+95',
                'label' => 'MM (+95)',
                'country' => 'Myanmar',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+960',
                'label' => 'MV (+960)',
                'country' => 'Maldives',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+961',
                'label' => 'LB (+961)',
                'country' => 'Lebanon',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+962',
                'label' => 'JO (+962)',
                'country' => 'Jordan',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+963',
                'label' => 'SY (+963)',
                'country' => 'Syria',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+964',
                'label' => 'IQ (+964)',
                'country' => 'Iraq',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+965',
                'label' => 'KW (+965)',
                'country' => 'Kuwait',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+966',
                'label' => 'SA (+966)',
                'country' => 'Saudi Arabia',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+967',
                'label' => 'YE (+967)',
                'country' => 'Yemen',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+968',
                'label' => 'OM (+968)',
                'country' => 'Oman',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+970',
                'label' => 'PS (+970)',
                'country' => 'Palestine',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+971',
                'label' => 'AE (+971)',
                'country' => 'United Arab Emirates',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+972',
                'label' => 'IL (+972)',
                'country' => 'Israel',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+973',
                'label' => 'BH (+973)',
                'country' => 'Bahrain',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+974',
                'label' => 'QA (+974)',
                'country' => 'Qatar',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+975',
                'label' => 'BT (+975)',
                'country' => 'Bhutan',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+976',
                'label' => 'MN (+976)',
                'country' => 'Mongolia',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+977',
                'label' => 'NP (+977)',
                'country' => 'Nepal',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+98',
                'label' => 'IR (+98)',
                'country' => 'Iran',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+992',
                'label' => 'TJ (+992)',
                'country' => 'Tajikistan',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+993',
                'label' => 'TM (+993)',
                'country' => 'Turkmenistan',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+994',
                'label' => 'AZ (+994)',
                'country' => 'Azerbaijan',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+995',
                'label' => 'GE (+995)',
                'country' => 'Georgia',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+996',
                'label' => 'KG (+996)',
                'country' => 'Kyrgyzstan',
                'regex' => $defaultRegex
            ],
            [
                'code' => '+998',
                'label' => 'UZ (+998)',
                'country' => 'Uzbekistan',
                'regex' => $defaultRegex
            ],
        ];
    }

    /**
     * Get country details by code
     *
     * @param string $code
     * @return array|null
     */
    public static function getByCode(string $code): ?array
    {
        $countries = collect(self::getAll());
        return $countries->firstWhere('code', $code);
    }

    /**
     * Get country details by country name
     *
     * @param string $country
     * @return array|null
     */
    public static function getByCountry(string $country): ?array
    {
        $countries = collect(self::getAll());
        return $countries->firstWhere('country', $country);
    }

    /**
     * Get only country codes
     *
     * @return array
     */
    public static function getCodes(): array
    {
        return collect(self::getAll())->pluck('code')->toArray();
    }

    /**
     * Get only country names
     *
     * @return array
     */
    public static function getCountries(): array
    {
        return collect(self::getAll())->pluck('country')->toArray();
    }

    /**
     * Validate phone number based on country code
     *
     * @param string $code Country code
     * @param string $number Phone number
     * @return bool
     */
    public static function validatePhoneNumber(string $code, string $number): bool
    {
        $country = self::getByCode($code);
        if (!$country) {
            return false;
        }

        return (bool) preg_match($country['regex'], $number);
    }

    /**
     * Get validation error message for phone number
     *
     * @param string $countryCode Country code
     * @param string $phoneNumber Phone number
     * @return string|null
     */
    public static function getPhoneNumberValidationError(string $countryCode, string $phoneNumber): ?string
    {
        $country = self::getByCode($countryCode);
        if (!$country) {
            return 'Invalid country code';
        }

        // Remove any non-digit characters from the phone number
        $cleanPhoneNumber = preg_replace('/[^\d]/', '', $phoneNumber);

        // Use the regex pattern from the country data
        if (!preg_match($country['regex'], $cleanPhoneNumber)) {
            // Get the regex pattern and extract digit requirements
            $pattern = $country['regex'];
            preg_match('/\d+/', $pattern, $digitMatches);
            $requiredDigits = $digitMatches[0] ?? '6';

            // Extract first digit requirements
            preg_match('/\[([^\]]+)\]/', $pattern, $firstDigitMatches);
            $firstDigits = $firstDigitMatches[1] ?? '0-9';

            // Build error message
            $message = "Invalid {$country['country']} phone number format. ";

            // Handle special cases
            if ($pattern === self::DEFAULT_REGEX) {
                $message .= "Must be at least 6 digits";
            } else if (strpos($pattern, ',') !== false) {
                // Handle ranges like 8-9 digits
                preg_match('/\{(\d+),(\d+)\}/', $pattern, $rangeMatches);
                $min = $rangeMatches[1];
                $max = $rangeMatches[2];
                $message .= "Must be {$min}-{$max} digits";

                if ($firstDigits !== '0-9') {
                    $message .= " starting with {$firstDigits}";
                }
            } else {
                // Standard case
                preg_match('/\{(\d+)\}/', $pattern, $exactMatches);
                $digits = $exactMatches[1] ?? $requiredDigits;
                $message .= "Must be {$digits} digits";

                if ($firstDigits !== '0-9') {
                    $message .= " starting with {$firstDigits}";
                }
            }


            return $message;
        }

        return null;
    }
}
