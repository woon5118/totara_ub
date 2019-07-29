<?php

use core\orm\query\builder;
use tassign_competency\expand_task;
use tassign_competency\models\assignment_actions;
use totara_job\job_assignment;

define('CLI_SCRIPT', 1);

require __DIR__.'/../../../config.php';
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir . '/phpunit/classes/util.php');

echo "This script will create data for the competency profile functionality testing and demo\n";
echo "Please create data on a clean site\n";

db()->transaction(Closure::fromCallable('create_data'));

function create_data() {

    $data = [
        'users' => [],
        'pos' => [],
        'orgs' => [],
        'audiences' => [],
        'scales' => [],
        'comps' => [],
    ];

    // First we need to create a few users
    $users = [
        'jm' => [
            'firstname' => 'John',
            'lastname' => 'Malkovich',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
        'ss' => [
            'firstname' => 'Steven',
            'lastname' => 'Seagal',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
        'dt' => [
            'firstname' => 'Denny',
            'lastname' => 'Trejo',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
        'jt' => [
            'firstname' => 'John',
            'lastname' => 'Travolta',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
        'ut' => [
            'firstname' => 'Uma',
            'lastname' => 'Thurman',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
        'sj' => [
            'firstname' => 'Samuel',
            'lastname' => 'Jackson',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
        'tr' => [
            'firstname' => 'Tim',
            'lastname' => 'Roth',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
        'bw' => [
            'firstname' => 'Bruce',
            'lastname' => 'Willis',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
        'vp' => [
            'firstname' => 'Vladimir',
            'lastname' => 'Putin',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
        'bo' => [
            'firstname' => 'Barack',
            'lastname' => 'Obama',
            'caps' => [
            ],
        ],
        'jb' => [
            'firstname' => 'George',
            'lastname' => 'Bush',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
        'gm' => [
            'firstname' => 'Glenn',
            'lastname' => 'Matthews',
            'caps' => [
                'totara/competency:view_own_profile'
            ],
        ],
    ];

    foreach ($users as $key => $user) {
        $data['users'][$key] = create_user($user);
    }

    // Then we need to create a few scale values
    $scales = [
        'low-scale' => [
            'name' => 'Low detail scale value',
            'description' => 'This is a rough competency scale value',
            'values' => [
                [
                    'name' => 'Competent',
                    'description' => 'Rough definition of being competent',
                    'proficient' => true,
                ],
                [
                    'name' => 'Not competent',
                    'description' => 'Rough definition of not being competent',
                ],
            ]
        ],

        'overboard-scale' => [
            'name' => 'Unnecessary detailed scale',
            'description' => 'This is a very descriptive competency scale value',
            'values' => [
                [
                    'name' => 'Extremely competent',
                    'description' => 'No doubt this fella is competent',
                    'proficient' => true,
                ],
                [
                    'name' => 'Competent',
                    'description' => 'There is some merit co call it competent',
                    'proficient' => true,
                ],
                [
                    'name' => 'Competent on Tuesdays',
                    'description' => 'Competent, but only on Tuesdays, do not ask why.',
                    'proficient' => true,
                ],
                [
                    'name' => 'Not competent on Tuesdays',
                    'description' => 'Not competent, but only on Tuesdays, do not ask why.',
                    'proficient' => true,
                ],
                [
                    'name' => 'Below average',
                    'description' => 'We can not call it competent for just yet, maybe wait for Tuesday.',
                ],
                [
                    'name' => 'Not competent',
                    'description' => 'Why does this value even exist?',
                ],
            ]
        ],

        '4-value-scale' => [
            'name' => '4 steps to success',
            'description' => 'Marketing driven scale',
            'values' => [
                [
                    'name' => 'Competent',
                    'description' => 'Competent. Full stop.',
                    'proficient' => true,
                ],
                [
                    'name' => 'Competent under supervision',
                    'description' => 'Success master supervision is required at all times',
                    'proficient' => true,
                ],
                [
                    'name' => 'On a path to success',
                    'description' => 'Not competent, but only on Tuesdays, do not ask why.',
                ],
                [
                    'name' => 'Newcomer',
                    'description' => 'Checkout is in the far left corner',
                ],
            ]
        ],

        'star-wars' => [
            'name' => 'To infinity and beyond',
            'description' => 'Force driven scale',
            'values' => [
                [
                    'name' => 'Sith Lord',
                    'description' => 'Cannot go beyond that. Do not confuse with sikh.',
                    'proficient' => true,
                ],
                [
                    'name' => 'Joined the dark side',
                    'description' => 'Clearly on a path to success, your lightsaber glows red now.',
                    'proficient' => true,
                ],
                [
                    'name' => 'Jedi',
                    'description' => 'It is pronounced Jed i',
                    'proficient' => true,
                ],
                [
                    'name' => 'Young padawan',
                    'description' => 'There is much to learn on your path. Do not kill your mentor.',
                ],
                [
                    'name' => 'Youngling',
                    'description' => 'If a new padawan puts a hood on - run away.',
                ],
            ]
        ],
    ];

    foreach ($scales as $key => $scale) {
        $data['scales'][$key] = create_scale($scale);
    }

    // Then we need to create a few competency frameworks
    $competencies = [
        'binary' => [
            'fullname' => 'Binary competencies',
            'description' => 'Descriptions that suppose to have competent or not only values',
            'scale' => $data['scales']['low-scale']->id,
            'competencies' => [
                'literate' => [
                    'fullname' => 'Being literate',
                    'description' => 'The name speaks for itself',
                ],

                'doer' => [
                    'fullname' => 'Complete tasks independently',
                    'description' => 'No need for constant badgering to complete a task',
                ],

                'initiative' => [
                    'fullname' => 'Show initiative and come up with ideas',
                    'description' => 'Being able to come up with ideas',
                ],

                'collider' => [
                    'fullname' => 'Manage The Large Hadron Collider at CERN',
                    'description' => 'Nothing too complicated, just another box ticked',
                ],
            ],
        ],

        'complex' => [
            'fullname' => 'Fine-grained competencies',
            'description' => 'Various competencies that require fine-tuned skills assessment to determine proficiency.',
            'scale' => $data['scales']['overboard-scale']->id,
            'competencies' => [
                'consultant' => [
                    'fullname' => 'Sales consultant',
                    'description' => 'It is not as easy as you think to become a sales consultant',
                ],

                'nurse' => [
                    'fullname' => 'Registered nurse',
                    'description' => 'You can not create content without adding a registered nurse in there',
                ],

                'administrative-nurse' => [
                    'fullname' => 'Registered administrative nurse',
                    'description' => 'Become an administrative registered nurse',
                ],

                'surgeon' => [
                    'fullname' => 'Fully qualified surgeon',
                    'description' => 'Surgeries are serious business',
                ],

                'priest' => [
                    'fullname' => 'Fully qualified reverent',
                    'description' => '9 circles of hell',
                ],

                'zoo-keeper' => [
                    'fullname' => 'Fully qualified zoo keeper',
                    'description' => 'Do not provoke the gator',
                ],

                'camp-ground-manager' => [
                    'fullname' => 'Fully qualified camp ground manager',
                    'description' => 'You know, this one is on the skilled migrant shortage list.',
                ],
            ],
        ],

        '4-value' => [
            'fullname' => 'Fantasy saga competencies',
            'description' => 'Mediocrity and courage',
            'scale' => $data['scales']['4-value-scale']->id,
            'competencies' => [
                'netflix' => [
                    'fullname' => 'Netflix Qualified',
                    'description' => 'It takes some skills to pick a show on Netflix, can you?',
                ],
                'shop-keeper' => [
                    'fullname' => 'Shop keeper',
                    'description' => 'Keep a hillbilly away and try to survive',
                ],
                'machinery-operator' => [
                    'fullname' => 'Heavy machinery operator on a rainy day',
                    'description' => 'Fire up the digger and start shoveling',
                ],
                'it' => [
                    'fullname' => 'Internet Troll',
                    'description' => 'Do you have what it takes to troll people on the Internet?',
                ],
                'sommelier' => [
                    'fullname' => 'Professional Sommelier',
                    'description' => 'Do you smell it?',
                ],
                'barista' => [
                    'fullname' => 'Professional Barista',
                    'description' => 'Please put your cups on the coffee machine to pre-warm it for the customers',
                ],
                'bartender' => [
                    'fullname' => 'Professional Bartender',
                    'description' => 'The first thing they teach you is not to drink on the job',
                ],
                'mad-preacher' => [
                    'fullname' => 'Mad preacher',
                    'description' => 'Nuff said, you must excel to be proficient in this discipline.',
                ],
            ]
        ],

        'star-wars' => [
            'fullname' => 'Fantasy saga competencies',
            'description' => 'In a galaxy far far away...',
            'scale' => $data['scales']['star-wars']->id,
            'competencies' => [
                'lightsaber' => [
                    'fullname' => 'Mastering a lightsaber',
                    'description' => 'It takes time to master an art of using lightsaber in a combat and make it effective against blasters',
                ],
                'pod-racer' => [
                    'fullname' => 'Pod racer',
                    'description' => 'On your path to success, you will need to master pod racing',
                ],
                'storm-trooper' => [
                    'fullname' => 'Qualified storm trooper',
                    'description' => 'Start a path to be a professional storm trooper, be above an average clone to stand out',
                ],
                'sith-lord' => [
                    'fullname' => 'Become the Sith Lord',
                    'description' => 'Begin your journey to become an evil mastermind starting in a dark corner of Tatooine',
                ],
            ]
        ],

        'arbitrary' => [
            'fullname' => 'Casual competencies',
            'description' => 'Something you might want to achieve casually',
            'scale' => 1,
            'competencies' => [
                'teeth-whitening' => [
                    'fullname' => 'Teeth whitening',
                    'description' => 'Professional dentists study for a long time to perform that',
                ],
                'hoarder' => [
                    'fullname' => 'Extra-compulsive hoarder',
                    'description' => 'I don\'t think that I can let this competency slide',
                ],
                'cc' => [
                    'fullname' => 'Couch critic',
                    'description' => 'This competency is more like an achievement',
                ],
            ]
        ],

        'bs' => [
            'fullname' => 'PDPD Behavioural Competency Guide',
            'description' => 'These are examples of the observable behaviours which relate to the competency. They are grouped and ordered to reflect complexity, level 1 being indicators for lower level jobs and level 4/5 indicators for senior or specialist roles and therefore demanding a higher level of competency, however, this does not mean for higher level roles the less complex indicators are not relevant or important. Note there is not a direct read across between the levels in the indicators and the grade structure, as some specialist roles at more junior levels may demand a higher level of application for some competencies. Therefore, the manager/reviewer and member of staff should have a discussion to agree the expected level of competency required for the role and level of the role holder. https://www.nottingham.ac.uk/hr/guidesandsupport/performanceatwork/pdpr/pdpr-behavioural-competency-guide/competency-framework.aspx',
            'scale' => 1,
            'competencies' => [
                // Achieving and delivery
                'drive' => [
                    'fullname' => 'Drive for Results',
                    'description' => 'Success is not just about following the rules. We need people committed to making the University a success. ‘Drive for results’ is the enthusiasms and desire to meet and exceed objectives, University targets and improve one’s own performance. It is about being frustrated with the status quo, wanting to improve the way we do things and making it happen. At a higher level it is about calculated risk taking in the interest of improving overall University performance.',
                ],
                'serving' => [
                    'fullname' => 'Serving the Customer',
                    'description' => 'This is the desire to anticipate, meet and exceed the needs and expectations of customers (internally and externally). It implies working together and building long-term customer relationships and focusing one\'s efforts on delivering increased customer value. At levels D and E it requires effective championing and partnership working.',
                ],
                'quality' => [
                    'fullname' => 'Quality Focus',
                    'description' => 'This is about demonstrating the underlying drive to ensure that quality is not compromised within the working environment. It includes the identification and maintenance of standards to meet the needs of the University, together with a desire for accuracy, order and safety in the workplace. At levels 3 and 4 it is about encouraging and monitoring the actions of others to maintain high standards.',
                ],
                'integrity' => [
                    'fullname' => 'Integrity',
                    'description' => 'This is about acting in a way that is consistent with what one says or values and the expectations of both the University and the HE Sector. It requires a demonstration of commitment to openness and ethical values. It includes taking time to respect and understand others and be transparent and honest in all dealing with people internal and external to the University.',
                ],

                // Personal effectiveness
                'planning' => [
                    'fullname' => 'Planning, organising and flexibility',
                    'description' => 'This is about adopting a methodical approach to work. It involves planning and organising oneself and others in order to deliver work and prevent future problems. This includes the ability to adapt and change plans as the requirements of the situation change. At the higher levels it involves thinking long-term, strategically and creatively.',
                ],
                'confidence' => [
                    'fullname' => 'Confidence and self-control',
                    'description' => 'This is a belief in one\'s own capability to accomplish a task and select an effective approach to a task or problem. This includes confidence in one\'s ability as expressed in increasingly challenging circumstances and confidence in one\'s decisions and opinions. The essence of this behaviour is the question, \'Does the person take on risky or difficulty tasks or measured conflicts with those in power over that person\'? Level D and E are primarily about assertiveness and confidence with one\'s boss or others in more senior positions, not with staff or peers.',
                ],
                'problem-solving' => [
                    'fullname' => 'Problem solving and initiative',
                    'description' => 'This is about engaging in proactive behaviour, seizing opportunities and originating action which goes beyond simply responding to the obvious needs of the situation or to direct requests from others. It is coming up with new or different ideas, or adapting ideas from elsewhere in the University or externally. It is concerned with moving the University forward by applying new ideas or old ideas in a new way to generate solutions and approaches. At the higher levels it is about thinking laterally and creating new concepts.',
                ],
                'info-seeking' => [
                    'fullname' => 'Critical information seeking',
                    'description' => 'Critical information seeking requires a selective approach to gathering information aimed at getting the really crucial pieces of information. The ability to seek out information based on an underlying curiosity or desire to know more about subject area, University issues, people, and the sector. It includes asking questions that go beyond what is routine, in order to \'dig\' or press for exact information. Critical information seeking is essential for making sure your decisions are firmly grounded in reality, and that they are the best they can be. ',
                ],

                // Working together
                'communication' => [
                    'fullname' => 'Communicating with clarity',
                    'description' => 'This is about the ability to impart accurate information (both verbal and written) in a timely way and be receptive to other peoples\' opinions. It is also about sharing information across University boundaries and externally. At the higher level, it is about making University communication and understanding with other bodies outside the University more effective.',
                ],
                'embracing' => [
                    'fullname' => 'Embracing change',
                    'description' => 'This is about the ability to make changes to the way you work, adapting to changing circumstances in the University by accepting new and different ideas and approaches. It includes the ability to sustain performance under conditions of rapid change. At higher levels, it is concerned with supporting others through change and having the willingness and ability to enable changes to take place in the most productive way.',
                ],
                'collaborating' => [
                    'fullname' => 'Collaborating with others',
                    'description' => 'This competency implies the intention of working co-operatively with others, to be part of a team, to work together as opposed to working separately or competitively. For this behaviour to be effective, the intention should be genuine. Team work and co-operation may be considered whenever the subject is a member of a group of people functioning as a team. This competency emphasises activity as a member of a group (rather than as a leader); e.g. Level E reflects a peer supporting their group rather than a leader managing the group.',
                ],
                'influencing' => [
                    'fullname' => 'Influencing and relationship building',
                    'description' => 'This is the ability to persuade, convince or influence others in order to get them to go along with or support a particular agenda, or get ‘buy in’ from others. It requires the ability to plan how to win support, gain co-operation or overcome barriers using a variety of approaches. Having gained support, it is the ability to build and maintain relationships with networks of people who may be able to effectively assist the organisation. At lower levels it is about presenting clear, logical arguments. At the higher level it requires taking a sophisticated strategic approach to influencing.',
                ],

                // Thinking and innovation
                'innovation' => [
                    'fullname' => 'Innovation and creativity',
                    'description' => 'This is about creating and identifying novel approaches to address challenging academic, technical or commercial situations and problems. It is about coming up with new or different ideas, or adapting ideas from elsewhere in the University or externally. It is concerned with moving the University forward by applying new ideas or old ideas in a new way to generate solutions and approaches. At the higher levels it is about thinking laterally and creating new concepts.',
                ],
                'thinking' => [
                    'fullname' => 'Conceptual and strategic thinking',
                    'description' => '	This is the ability to see things as a whole, identify key issues, see relationships and draw elements together into broad coherent frameworks. This competency describes the ability to relate different events and key pieces of information; to make connections, see patterns and trends; to draw information together into models and frameworks which can then be used to interpret complex situations and identify their salient features. The strategic element involves looking into the future, considering the future needs of the University, Faculty or Department and thinking about how present policies, processes and methods might be progressively affected by future developments and trends; developing long term goals and strategies extending over significant time-spans.',
                ],

                // Managing, leading and developing others
                'managing' => [
                    'fullname' => 'Managing and leading the team',
                    'description' => 'Leading a team or function is about managing and developing others. This competency therefore reflects that to get the best out of people we need to build and integrate all aspects of the performance cycle, including:
<ul>
    <li>Being clear about what has to be achieved</li>
    <li>Assembling the necessary resources to meet what has to be done</li>
    <li>Monitoring and addressing gaps in staff development and performance</li>
    <li>Reviewing this people/work match in the light of setting future objectives and leading change to meet University needs.</li>
</ul>',
                ],
            ]
        ]
    ];

    foreach ($competencies as $key => $framework) {
        $data['comps'][$key] = create_competency_framework($framework);
    }

    // Then we need to create a few positions
    $positions = [
        'pp' => [
            'fullname' => 'Primary positions',
                'description' => 'The positions we can not live without...',
                'positions' => [
                    'janitor' => [
                        'fullname' => 'Janitor',
                        'description' => 'No institution or company can survive without a clean closet',
                        'members' => [
                            'gm' => get_user('gm', $data),
                        ],
                        'competencies' => [
                            'priest' => get_competency('complex', 'priest', $data),
                            'teeth-whitening' => get_competency('arbitrary', 'teeth-whitening', $data),
                            'hoarder' => get_competency('arbitrary', 'hoarder', $data),
                            'netflix' => get_competency('4-value', 'netflix', $data),
                        ],
                    ],
                    'stargazer' =>  [
                        'fullname' => 'Stargazer',
                        'description' => 'There is no point in arguing that this is very important',
                        'members' => [
                            'jm' => get_user('jm', $data),
                            'ss' => get_user('ss', $data),
                            'ut' => get_user('ut', $data),
                            'tr' => get_user('tr', $data),
                        ],
                        'competencies' => [
                            'doer' => get_competency('binary', 'doer', $data),
                            'hoarder' => get_competency('arbitrary', 'hoarder', $data),
                            'administrative-nurse' => get_competency('complex', 'administrative-nurse', $data),
                            'it' => get_competency('4-value', 'it', $data),
                            'storm-trooper' => get_competency('star-wars', 'storm-trooper', $data),
                            'integrity' => get_competency('bs', 'integrity', $data),
                        ],
                    ],
                    'theologist' => [
                        'fullname' => 'Theologist',
                        'description' => 'Endless conversations about religion with a drink in the middle',
                        'members' => [
                            'jm' => get_user('jm', $data),
                            'dt' => get_user('dt', $data),
                            'ss' => get_user('ss', $data),
                            'tr' => get_user('tr', $data),
                            'sj' => get_user('sj', $data),
                            'ut' => get_user('ut', $data),
                        ],
                        'competencies' => [
                            'literate' => get_competency('binary', 'literate', $data),
                            'cc' => get_competency('arbitrary', 'cc', $data),
                            'consultant' => get_competency('complex', 'consultant', $data),
                            'drive' => get_competency('bs', 'drive', $data),
                        ],
                    ],
                    'meter-reader' => [
                        'fullname' => 'Meter reader',
                        'description' => 'Reading meters is a unique art of getting analogue or digital readings from various types of meters',
                        'members' => [
                            'jm' => get_user('jm', $data),
                            'ss' => get_user('ss', $data),
                            'tr' => get_user('tr', $data),
                            'ut' => get_user('ut', $data),
                            'bo' => get_user('bo', $data),
                        ],
                        'competencies' => [
                            'literate' => get_competency('binary', 'literate', $data),
                            'cc' => get_competency('arbitrary', 'cc', $data),
                            'consultant' => get_competency('complex', 'consultant', $data),
                            'shop-keeper' => get_competency('4-value', 'shop-keeper', $data),
                            'lightsaber' => get_competency('star-wars', 'lightsaber', $data),
                            'drive' => get_competency('bs', 'drive', $data),
                        ],
                    ],
                    'analyst' => [
                        'fullname' => 'Static asset analyst in the dynamic environment',
                        'description' => 'Analyzing assets statically is quite important in the dynamic environment of our modern ever-changing world',
                        'members' => [
                            'jm' => get_user('jm', $data),
                            'dt' => get_user('dt', $data),
                            'bo' => get_user('bo', $data),
                            'tr' => get_user('tr', $data),
                            'ss' => get_user('ss', $data),
                            'ut' => get_user('ut', $data),
                        ],
                        'competencies' => [
                            'netflix' => get_competency('4-value', 'netflix', $data),
                            'shop-keeper' => get_competency('4-value', 'shop-keeper', $data),
                            'machinery-operator' => get_competency('4-value', 'machinery-operator', $data),
                            'it' => get_competency('4-value', 'it', $data),
                            'sommelier' => get_competency('4-value', 'sommelier', $data),
                            'barista' => get_competency('4-value', 'barista', $data),
                            'bartender' => get_competency('4-value', 'bartender', $data),
                            'mad-preacher' => get_competency('4-value', 'mad-preacher', $data),
                            'teeth-whitening' => get_competency('arbitrary', 'teeth-whitening', $data),
                            'hoarder' => get_competency('arbitrary', 'hoarder', $data),
                            'cc' => get_competency('arbitrary', 'cc', $data),
                            'consultant' => get_competency('complex', 'consultant', $data),
                            'nurse' => get_competency('complex', 'nurse', $data),
                            'administrative-nurse' => get_competency('complex', 'administrative-nurse', $data),
                            'surgeon' => get_competency('complex', 'surgeon', $data),
                            'camp-ground-manager' => get_competency('complex', 'camp-ground-manager', $data),
                        ],
                    ],
                ],
            ],
        'sp' => [
            'fullname' => 'Secondary positions',
            'descriptions' => 'These positions are also important, but not as much as primary positions',
            'positions' => [
                'ceo' => [
                    'fullname' => 'CEO',
                    'description' => 'Chief Executive Officer',
                    'members' => [
                        'jt' => get_user('jt', $data),
                        'ut' => get_user('ut', $data),
                        'bo' => get_user('bo', $data),
                    ],
                    'competencies' => [
                        'literate' => get_competency('binary', 'literate', $data),
                        'cc' => get_competency('arbitrary', 'cc', $data),
                        'consultant' => get_competency('complex', 'consultant', $data),
                        'shop-keeper' => get_competency('4-value', 'shop-keeper', $data),
                        'lightsaber' => get_competency('star-wars', 'lightsaber', $data),
                        'drive' => get_competency('bs', 'drive', $data),
                    ],
                ],
                'chief-accountant' => [
                    'fullname' => 'Chief Accountant',
                    'description' => 'Very important accountant',
                    'members' => [
                        'jt' => get_user('jt', $data), // To assign and archive
                        'jb' => get_user('jb', $data),
                        'bo' => get_user('bo', $data),
                    ],
                    'competencies' => [
                        'zoo-keeper' => get_competency('complex', 'zoo-keeper', $data),
                        'collider' => get_competency('binary', 'collider', $data),
                        'sommelier' => get_competency('4-value', 'sommelier', $data),
                        'pod-racer' => get_competency('star-wars', 'pod-racer', $data),
                    ]
                ],
                'accountant' => [
                    'fullname' => 'Regular accountant',
                    'description' => 'Not as important as chief accountant, but still pretty important',
                ],
                'hr' => [
                    'fullname' => 'Human Relation Manager',
                    'description' => 'No one can survive without HR manager, especially the one making decisions without being competent in the area',
                    'members' => [
                        'jb' => get_user('jb', $data), // To assign and archive
                        'bo' => get_user('bo', $data),
                    ],
                    'competencies' => [
                        'literate' => get_competency('binary', 'literate', $data),
                        'doer' => get_competency('binary', 'doer', $data),
                        'initiative' => get_competency('binary', 'initiative', $data),
                        'collider' => get_competency('binary', 'collider', $data),
                    ],
                ],
            ]
        ]
    ];

    foreach ($positions as $key => $position) {
        $data['pos'][$key] = create_position_framework($position);
    }

    // Then we need to create a few organisations
    $organisations = [
        'europe' => [
            'fullname' => 'European organizations',
            'description' => 'The organisations we are relying upon in Europe',
            'organisations' => [
                'wwf' => [
                    'fullname' => 'Word Wildlife Fund',
                    'description' => 'The panda on the logo is so cute...',
                    'members' => [
                        'jm' => get_user('jm', $data),
                        'dt' => get_user('dt', $data),
                        'tr' => get_user('tr', $data),
                        'ut' => get_user('ut', $data),
                        'ss' => get_user('ss', $data),
                    ],
                    'competencies' => [
                        'nurse' => get_competency('complex', 'nurse', $data),
                        'surgeon' => get_competency('complex', 'surgeon', $data),
                        'priest' => get_competency('complex', 'priest', $data),
                        'zoo-keeper' => get_competency('complex', 'zoo-keeper', $data),
                        'camp-ground-manager' => get_competency('complex', 'camp-ground-manager', $data),
                        'barista' => get_competency('4-value', 'barista', $data),
                        'it' => get_competency('4-value', 'it', $data),
                        'shop-keeper' => get_competency('4-value', 'shop-keeper', $data),
                        'netflix' => get_competency('4-value', 'netflix', $data),
                    ],
                ],
                'cola' =>  [
                    'fullname' => 'Coca-Cola European Partners',
                    'description' => 'Coca-Cola European Partners plc is a multinational bottling company dedicated to the marketing, production, and distribution of Coca-Cola products. Wikipedia',
                    'members' => [
                        'jm' => get_user('jm', $data),
                        'bo' => get_user('bo', $data),
                        'ss' => get_user('ss', $data),
                        'dt' => get_user('dt', $data),
                    ],
                    'competencies' => [
                        'literate' => get_competency('binary', 'literate', $data),
                        'doer' => get_competency('binary', 'doer', $data),
                        'initative' => get_competency('binary', 'initiative', $data),
                        'collider' => get_competency('binary', 'collider', $data),
                        'mad-preacher' => get_competency('4-value', 'mad-preacher', $data),
                        'bartender' => get_competency('4-value', 'bartender', $data),
                    ],
                ],
                'nestle' => [
                    'fullname' => 'Nestlé',
                    'description' => 'It is like Nescafe, but better',
                    'members' => [
                        'jm' => get_user('jm', $data),
                        'tr' => get_user('tr', $data),
                        'ut' => get_user('ut', $data),
                        'ss' => get_user('ss', $data),
                    ],
                    'competencies' => [

                    ],
                ],
                'nescafe' => [
                    'fullname' => 'Nescafé',
                    'description' => 'It is like Nestlé, but wait it is a part of Nesrlé...',
                    'members' => [
                        'jm' => get_user('jm', $data),
                        'ss' => get_user('ss', $data),
                        'tr' => get_user('tr', $data),
                        'bo' => get_user('bo', $data),
                        'ut' => get_user('ut', $data),
                        'sj' => get_user('sj', $data),
                    ],
                    'competencies' => [
                        'storm-trooper' => get_competency('star-wars', 'storm-trooper', $data),
                        'sith-lord' => get_competency('star-wars', 'sith-lord', $data),
                        'communication' => get_competency('bs', 'communication', $data),
                        'embracing' => get_competency('bs', 'embracing', $data),
                        'mad-preacher' => get_competency('4-value', 'mad-preacher', $data),
                        'bartender' => get_competency('4-value', 'bartender', $data),
                        'collaborating' => get_competency('bs', 'collaborating', $data),
                    ]
                ],
                'mercedes' => [
                    'fullname' => 'Mercedes Benz',
                    'description' => 'A division of Daimler',
                    'members' => [
                        'jm' => get_user('jm', $data),
                        'dt' => get_user('dt', $data),
                        'tr' => get_user('tr', $data),
                        'bo' => get_user('bo', $data),
                        'ss' => get_user('ss', $data),
                    ],
                    'competencies' => [
                        'lightsaber' => get_competency('star-wars', 'lightsaber', $data),
                        'pod-racer' => get_competency('star-wars', 'pod-racer', $data),
                        'storm-trooper' => get_competency('star-wars', 'storm-trooper', $data),
                        'sith-lord' => get_competency('star-wars', 'sith-lord', $data),
                    ]
                ],
            ],
        ],
        'na' => [
            'fullname' => 'North America',
            'descriptions' => 'We favour these in the North America',
            'organisations' => [
                'greenpeace' => [
                    'fullname' => 'GreenPeace',
                    'description' => 'Keeping peace, doing green stuff',
                ],
                'cola' => [
                    'fullname' => 'Coca Cola LLC LTD etc',
                    'description' => 'Nothing beats a warm cola on a hot day',
                ],
                'apple' => [
                    'fullname' => 'Apple',
                    'description' => 'Apple is not the same without Steve',
                ],
                'google' => [
                    'fullname' => 'Google',
                    'description' => 'Please update your Chrome Browser',
                ],
            ]
        ]
    ];

    foreach ($organisations as $key => $organisation) {
        $data['orgs'][$key] = create_position_framework($organisation);
    }

    // Then we need to create a few audiences
    $audiences = [
        'cr' => [
            'name' => 'Content makers',
            'description' => 'This audience is for creative staff members',
            'members' => [
                'jm' => get_user('jm', $data),
                'ss' => get_user('ss', $data),
                'dt' => get_user('dt', $data),
                'ut' => get_user('ut', $data),
                'sj' => get_user('sj', $data),
            ],
            'competencies' => [
                'drive' => get_competency('bs', 'drive', $data),
                'serving' => get_competency('bs', 'serving', $data),
                'quality' => get_competency('bs', 'quality', $data),
                'integrity' => get_competency('bs', 'integrity', $data),
                'planning' => get_competency('bs', 'planning', $data),
                'confidence' => get_competency('bs', 'confidence', $data),
                'problem-solving' => get_competency('bs', 'problem-solving', $data),
                'info-seeking' => get_competency('bs', 'info-seeking', $data),
                'communication' => get_competency('bs', 'communication', $data),
                'embracing' => get_competency('bs', 'embracing', $data),
                'collaborating' => get_competency('bs', 'collaborating', $data),
                'influencing' => get_competency('bs', 'influencing', $data),
                'innovation' => get_competency('bs', 'innovation', $data),
                'thinking' => get_competency('bs', 'thinking', $data),
                'managing' => get_competency('bs', 'managing', $data),
            ],
        ],
        'it' => [
            'name' => 'IT Department',
            'description' => 'Every respectful company needs to have at least one in-house IT department',
            'members' => [
                'jm' => get_user('jm', $data),
                'ss' => get_user('ss', $data),
            ],
            'competencies' => [
                'zoo-keeper' => get_competency('complex', 'zoo-keeper', $data),
                'collider' => get_competency('binary', 'collider', $data),
                'sommelier' => get_competency('4-value', 'sommelier', $data),
                'barista' => get_competency('4-value', 'barista', $data),
                'it' => get_competency('4-value', 'it', $data),
                'machinery-operator' => get_competency('4-value', 'machinery-operator', $data),
                'pod-racer' => get_competency('star-wars', 'pod-racer', $data),
                'hoarder' => get_competency('arbitrary', 'hoarder', $data),
                'cc' => get_competency('arbitrary', 'cc', $data),
            ]
        ],
        'vip' => [
            'name' => 'VIP',
            'description' => 'Privileged members group',
            'members' => [
                'jm' => get_user('jm', $data),
                'ss' => get_user('ss', $data),
                'dt' => get_user('dt', $data),
            ],
            'competencies' => [
                'lightsaber' => get_competency('star-wars', 'lightsaber', $data),
                'pod-racer' => get_competency('star-wars', 'pod-racer', $data),
                'storm-trooper' => get_competency('star-wars', 'storm-trooper', $data),
                'sith-lord' => get_competency('star-wars', 'sith-lord', $data),

                'consultant' => get_competency('complex', 'consultant', $data),
                'nurse' => get_competency('complex', 'nurse', $data),
                'administrative-nurse' => get_competency('complex', 'administrative-nurse', $data),
            ],
        ],
    ];

    foreach ($audiences as $key => $audience) {
        $data['audiences'][$key] = create_audience($audience);
    }

    // Let's run expand task
    (new expand_task(db()))->expand_all();

    // Then we need to create achievement records
    $achievements = [
        // Janitor
        [get_user('gm', $data), get_assignment('pos', 'pp', 'janitor', 'priest', $data), 3],
        [get_user('gm', $data), get_assignment('pos', 'pp', 'janitor', 'teeth-whitening', $data), 2],
        [get_user('gm', $data), get_assignment('pos', 'pp', 'janitor', 'hoarder', $data), 3],

        // Stargazer
        // JM
        [get_user('jm', $data), get_assignment('pos', 'pp', 'stargazer', 'doer', $data), 1],
        [get_user('jm', $data), get_assignment('pos', 'pp', 'stargazer', 'hoarder', $data), 1],
        [get_user('jm', $data), get_assignment('pos', 'pp', 'stargazer', 'administrative-nurse', $data), 1],
        [get_user('jm', $data), get_assignment('pos', 'pp', 'stargazer', 'it', $data), 1],
        [get_user('jm', $data), get_assignment('pos', 'pp', 'stargazer', 'storm-trooper', $data), 1],
        [get_user('jm', $data), get_assignment('pos', 'pp', 'stargazer', 'integrity', $data), 1],

        //SS
        [get_user('ss', $data), get_assignment('pos', 'pp', 'stargazer', 'doer', $data), 2],
        [get_user('ss', $data), get_assignment('pos', 'pp', 'stargazer', 'administrative-nurse', $data), 3],
        [get_user('ss', $data), get_assignment('pos', 'pp', 'stargazer', 'storm-trooper', $data), 1],
        [get_user('ss', $data), get_assignment('pos', 'pp', 'stargazer', 'integrity', $data), 3],

        //UT
        [get_user('ut', $data), get_assignment('pos', 'pp', 'stargazer', 'hoarder', $data), 2],
        [get_user('ut', $data), get_assignment('pos', 'pp', 'stargazer', 'it', $data), 3],
        [get_user('ut', $data), get_assignment('pos', 'pp', 'stargazer', 'storm-trooper', $data), 3],
        [get_user('ut', $data), get_assignment('pos', 'pp', 'stargazer', 'integrity', $data), 3],

        //TR
        [get_user('tr', $data), get_assignment('pos', 'pp', 'stargazer', 'hoarder', $data), 1],
        [get_user('tr', $data), get_assignment('pos', 'pp', 'stargazer', 'it', $data), 3],
        [get_user('tr', $data), get_assignment('pos', 'pp', 'stargazer', 'storm-trooper', $data), 1],
        [get_user('tr', $data), get_assignment('pos', 'pp', 'stargazer', 'integrity', $data), 2],

        //Theologist

        //JM
        [get_user('jm', $data), get_assignment('pos', 'pp', 'theologist', 'literate', $data), 1],
        [get_user('jm', $data), get_assignment('pos', 'pp', 'theologist', 'cc', $data), 1],
        [get_user('jm', $data), get_assignment('pos', 'pp', 'theologist', 'consultant', $data), 1],
        [get_user('jm', $data), get_assignment('pos', 'pp', 'theologist', 'drive', $data), 1],

        //DT
        [get_user('dt', $data), get_assignment('pos', 'pp', 'theologist', 'cc', $data), 2],
        [get_user('dt', $data), get_assignment('pos', 'pp', 'theologist', 'consultant', $data), 4],
        [get_user('dt', $data), get_assignment('pos', 'pp', 'theologist', 'drive', $data), 2],

        //SS
        [get_user('ss', $data), get_assignment('pos', 'pp', 'theologist', 'literate', $data), 1],
        [get_user('ss', $data), get_assignment('pos', 'pp', 'theologist', 'cc', $data), 3],
        [get_user('ss', $data), get_assignment('pos', 'pp', 'theologist', 'consultant', $data), 2],

        //TR
        [get_user('tr', $data), get_assignment('pos', 'pp', 'theologist', 'literate', $data), 1],
        [get_user('tr', $data), get_assignment('pos', 'pp', 'theologist', 'drive', $data), 1],

        //SJ
        [get_user('sj', $data), get_assignment('pos', 'pp', 'theologist', 'literate', $data), 2],
        [get_user('sj', $data), get_assignment('pos', 'pp', 'theologist', 'cc', $data), 2],
        [get_user('sj', $data), get_assignment('pos', 'pp', 'theologist', 'drive', $data), 3],

        //UT
        [get_user('ut', $data), get_assignment('pos', 'pp', 'theologist', 'literate', $data), 1],
        [get_user('ut', $data), get_assignment('pos', 'pp', 'theologist', 'cc', $data), 2],
        [get_user('ut', $data), get_assignment('pos', 'pp', 'theologist', 'consultant', $data), 4],
        [get_user('ut', $data), get_assignment('pos', 'pp', 'theologist', 'drive', $data), 2],
    ];

    foreach ($achievements as $achievement) {
        create_achievement_record(...$achievement);
    }

    // Then let's archive some assignments
    $archive = [
        // Theologist POS
        [get_assignment('pos', 'pp', 'theologist', 'cc', $data)],
        [get_assignment('pos', 'pp', 'theologist', 'drive', $data), true],

        // IT audience
        [get_assignment('audience', '', 'it', 'collider', $data)],
        [get_assignment('audience', '', 'it', 'barista', $data), true],

        // VIP audience
        [get_assignment('audience', '', 'vip', 'consultant', $data), true],
        [get_assignment('audience', '', 'vip', 'nurse', $data)],
    ];

    foreach ($archive as $ass) {
        if (!is_array($ass)) {
            $ass = [$ass];
        }

        archive_assignment($ass[0], $ass[1] ?? false);
    }
}

/**
 * Create a user and assign given capabilities (if any)
 *
 * @param array $attributes
 * @return stdClass
 */
function create_user($attributes) {
    $user = generator()->create_user($attributes);

    if (is_array($attributes['caps'] ?? null)) {
        // Let's allow the user what we want to.

        $role = db()->get_record('role', ['shortname'=>'user']);

        foreach ($attributes['caps'] as $cap) {
            if (!is_array($cap)) {
                $cap = [$cap];
            }

            assign_capability($cap[0], CAP_ALLOW, $role->id, $cap[1] ?? context_system::instance());
        }
    }

    return $user;
}

/**
 * Create competency framework
 *
 * @param array $attributes
 * @return stdClass
 */
function create_competency_framework($attributes) {
    $framework = hierarchy_generator()->create_comp_frame($attributes);

    if (is_array($attributes['competencies'] ?? null)) {
        $framework->competencies = [];

        foreach ($attributes['competencies'] as $key => $competency) {
            $framework->competencies[$key] = create_competency($competency, $framework);
        }
    }

    return $framework;
}

/**
 * Create competency
 *
 * @param array $attributes
 * @param stdClass $framework
 * @return stdClass
 */
function create_competency($attributes, $framework) {
    $attributes['frameworkid'] = $framework->id;

    return hierarchy_generator()->create_comp($attributes);
}

/**
 * Create competency scale
 *
 * @param $attributes
 * @return stdClass
 */
function create_scale($attributes) {

    if (is_array($attributes['values'] ?? null)) {
        $last_order = 1;

        foreach ($attributes['values'] as &$value) {
            if (isset($value['sortorder'])) {
                $last_order = $value['sortorder'];
            } else {
                $value['sortorder'] = $last_order;
                $last_order += 1;
            }
        }
    }

    $scale = hierarchy_generator()->create_scale('comp', $attributes, $attributes['values'] ?? []);

    $scale->values = db()->get_records('comp_scale_values', ['scaleid' => $scale->id], 'sortorder desc');

    return $scale;
}

/**
 * Create position
 *
 * @param array $attributes
 * @param stdClass $framework
 * @return stdClass
 */
function create_position($attributes, $framework) {
    $attributes['frameworkid'] = $framework->id;

    $pos = hierarchy_generator()->create_pos($attributes);

    if (is_array($attributes['members'] ?? null)) {
        foreach ($attributes['members'] as $member) {
            create_job_assignment('position', $pos, $member);
        }
    }

    if (is_array($attributes['competencies'] ?? null)) {
        $pos->competencies = [];

        foreach ($attributes['competencies'] as $key => $competency) {
            $pos->competencies[$key] = assignment_generator()->create_position_assignment($competency->id, $pos->id);
        }
    }

    return $pos;
}

/**
 * Create position framework
 *
 * @param array $attributes
 * @return stdClass
 */
function create_position_framework($attributes) {
    $framework = hierarchy_generator()->create_pos_frame($attributes);

    $framework->positions = [];

    if (is_array($attributes['positions'] ?? null)) {
        foreach ($attributes['positions'] as $key => $position) {
            $framework->positions[$key] = create_position($position, $framework);
        }
    }

    return $framework;
}

/**
 * Create organisation
 *
 * @param array $attributes
 * @param stdClass $framework
 * @return stdClass
 */
function create_organisation($attributes, $framework) {
    $attributes['frameworkid'] = $framework->id;

    $org = hierarchy_generator()->create_org($attributes);

    if (is_array($attributes['members'] ?? null)) {
        $org->assignments = [];

        foreach ($attributes['members'] as $member) {
            create_job_assignment('organisation', $org, $member);
        }
    }

    if (is_array($attributes['competencies'] ?? null)) {
        $org->competencies = [];

        foreach ($attributes['competencies'] as $key => $competency) {
            $org->competencies[$key] = assignment_generator()->create_organisation_assignment($competency->id, $org->id);
        }
    }

    return $org;
}

/**
 * Create organisation framework
 *
 * @param array $attributes
 * @return stdClass
 */
function create_organisation_framework($attributes) {
    $framework =  hierarchy_generator()->create_org_frame($attributes);

    $framework->organisations = [];

    if (is_array($attributes['organisations'] ?? null)) {
        foreach ($attributes['organisations'] as $key => $organisation) {
            $framework->organisations[$key] = create_organisation($organisation, $framework);
        }
    }

    return $framework;
}


/**
 * Create a job assignment for position organisation
 *
 * @param string $type Type organisation \ position
 * @param stdClass $target
 * @param stdClass $user
 * @return job_assignment
 */
function create_job_assignment(string $type, $target, $user) {
    $type = substr($type, 0 ,3);

    if (!in_array($type, ['org', 'pos'])) {
        throw new coding_exception('It is not supported currently to create a job assignment for "' . $type . '" type');
    }

    $get_id_column = function ($type) {
        switch ($type) {
            case 'pos':
                return 'positionid';
            case 'org':
                return 'organisationid';
            default:
                throw new coding_exception('It is not supported currently to create a job assignment for "' . $type . '" type');
        }
    };

    return job_assignment::create([
        'userid' => $user->id ,
        'idnumber' => 'ja_for_org_ass_' . $target->id . '_for_' . $user->id,
        'fullname' => 'Assigning ' . $type . ' ' . $target->fullname,
        $get_id_column($type) => $target->id
    ]);
}

/**
 * Create audience
 *
 * @param $attributes
 * @return stdClass
 */
function create_audience($attributes) {
    $audience = generator()->create_cohort($attributes);

    if (is_array($attributes['members'] ?? null)) {
        foreach ($attributes['members'] as $member) {
            cohort_add_member($audience->id, $member->id);
        }
    }

    if (is_array($attributes['competencies'] ?? null)) {
        $audience->competencies = [];

        foreach ($attributes['competencies'] as $key => $competency) {
            $audience->competencies[$key] = assignment_generator()->create_cohort_assignment($competency->id, $audience->id);
        }
    }

    return $audience;
}

function create_individual_assignment($competency, $user) {
    return assignment_generator()->create_user_assignment($competency->id, $user->id);
}

/**
 * Get user from data
 *
 * @param $key
 * @param $data
 * @return stdClass|null
 */
function get_user($key, $data) {
    $user = $data['users'][$key] ?? null;

    if (is_null($user)) {
        throw new Exception('Requested user $data[\'users\'][\'' . $key , '\'] not found');
    }

    return $user;
}

/**
 * Get competency from data
 *
 * @param $fw
 * @param $key
 * @param $data
 * @return stdClass|null
 */
function get_competency($fw, $key, $data) {
    $comp = $data['comps'][$fw]->competencies[$key] ?? null;

    if (is_null($comp)) {
        throw new Exception('Requested competency $data[\'comps\'][\'' . $fw . '\']->competencies[\'' . $key . '\'] not found');
    }

    return $comp;
}

/**
 * Get competency from data
 *
 * @param string $type
 * @param string $fw
 * @param $item
 * @param string $key
 * @param array $data
 * @return stdClass|null
 */
function get_assignment($type, $fw, $item, $key, $data) {

    if ($type === 'audience') {
        $ass = $data['audiences'][$item]->competencies[$key] ?? null;
        $error = 'Requested assignment $data[\'' . $type . '\'][\'' . $item . '\']->competencies[\'' . $key . '\'] not found';
    } else {

        $plural = '';
        switch ($type) {
            case 'audience':
                $plural = 'audiences';
                break;
            case 'pos':
                $plural = 'positions';
                break;
            case 'orgs':
                $plural = 'organisations';
                break;

            default:
                throw new Exception('Unknown type given: ', $type);
        }

        $error = 'Requested assignment $data[\'' . $type . '\'][\'' . $fw . '\']->' . $plural . '[\'' . $item . '\']->competencies[\'' . $key . '\'] not found';

        $ass = $data[$type][$fw]->{$plural}[$item]->competencies[$key] ?? null;
    }

    if (is_null($ass)) {
        throw new Exception($error);
    }

    return $ass;
}

function archive_assignment($assignment, $continue_tracking = false) {
    $actions = new assignment_actions();
    $ids = $actions->archive($assignment->id, $continue_tracking);
    sleep(1);
    (new expand_task(db()))->expand_all();
    return !empty($ids);
}

function create_achievement_record($user, $assignment, $value) {

    $comp = builder::table('comp')->where('id', $assignment->competency_id)->one();

    $value = builder::table('comp_scale_values')
        ->join('comp_scale', 'scaleid', 'id')
        ->join('comp_scale_assignments', 'comp_scale.id', 'scaleid')
        ->where('comp_scale_assignments.frameworkid', $comp->frameworkid)
        ->where('sortorder', $value)
        ->one();

    if (is_null($value)) {
        throw new \Exception('Error getting value for: ' . $comp->fullname . ', sort order: ' . $value);
    }

    builder::table('totara_competency_achievement')
        ->insert([
            'comp_id' => $comp->id,
            'user_id' => $user->id,
            'assignment_id' => $assignment->id,
            'scale_value_id' => $value->id,
            'proficient' => $value->proficient,
            'status' => 0,
            'time_created' => time(),
            'time_status' => time(),
            'time_proficient' => time(),
            'time_scale_value' => time(),
            'last_aggregated' => time(),
        ]);
}


/**
 * Return an instance of testing data generator
 *
 * @return testing_data_generator
 */
function generator() {
    return phpunit_util::get_data_generator();
}

/**
 * Get Hierarchy specific generator
 *
 * @return totara_hierarchy_generator
 */
function hierarchy_generator() {
    return generator()->get_plugin_generator('totara_hierarchy');
}

/**
 * Get Assignment specific generator
 *
 * @return tassign_competency_generator
 */
function assignment_generator() {
    return generator()->get_plugin_generator('tassign_competency');
}

/**
 * Return an instance for Moodle Database
 *
 * @return moodle_database
 */
function db() {
    return $GLOBALS['DB'];
}