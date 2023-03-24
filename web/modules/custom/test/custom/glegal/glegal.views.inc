<?php

/**
 * @file
 * Views handler and plugin definitions for the glegal module.
 */

/**
 * Implements hook_views_data().
 */
function glegal_views_data() {
  $data                                      = [];
  $data['glegal_accepted']['table']['group'] = t('glegal');

  $data['glegal_accepted']['table']['join']['users'] = [
    'left_field' => 'uid',
    'field'      => 'uid',
  ];

  $data['users']['glegal_accepted_rel'] = [
    'real field'   => 'uid',
    'title'        => t('accepted user'),
    'group'        => t('glegal'),
    'help'         => t('An accepted user'),
    'relationship' => [
      'id'                 => 'standard',
      'base'               => 'glegal_accepted',
      'base field'         => 'uid',
      'label'              => t('glegal'),
      'relationship field' => 'uid',
    ],
  ];

  $data['glegal_accepted']['glegal_conditions_rel'] = [
    'group'        => t('glegal'),
    'title'        => t('glegal Conditions relationship'),
    'help'         => t('Create a relationship to a glegal conditions and glegal accepted.'),
    'relationship' => [
      'id'                 => 'standard',
      'base'               => 'glegal_conditions',
      'base field'         => 'tc_id',
      'label'              => t('glegal'),
      'relationship field' => 'tc_id',
    ],
  ];

  $data['glegal_accepted']['version'] = [
    'title'    => t('Version'),
    'help'     => t('Version ID accepted.'),
    'field'    => [
      'id'             => 'standard',
      'click sortable' => TRUE,
    ],
    'filter'   => [
      'id'          => 'numeric',
      'allow empty' => TRUE,
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'sort'     => [
      'id' => 'standard',
    ],
  ];

  $data['glegal_accepted']['revision'] = [
    'title'    => t('Revision'),
    'help'     => t('Revision ID accepted.'),
    'field'    => [
      'id'             => 'standard',
      'click sortable' => TRUE,
    ],
    'filter'   => [
      'id'          => 'numeric',
      'allow empty' => TRUE,
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'sort'     => [
      'id' => 'standard',
    ],
  ];

  if (\Drupal::moduleHandler()->moduleExists('locale')) {
    $data['glegal_accepted']['language'] = [
      'title'    => t('Language'),
      'help'     => t('Language of T&C accepted by user.'),
      'field'    => [
        'id'             => 'language',
        'click sortable' => TRUE,
      ],
      'filter'   => [
        'id'          => 'language',
        'allow empty' => TRUE,
      ],
      'argument' => [
        'id' => 'language',
      ],
      'sort'     => [
        'id' => 'standard',
      ],
    ];
  }

  $data['glegal_accepted']['uid'] = [
    'title'        => t('User ID'),
    'help'         => t('User ID of user that accepted.'),
    'field'        => [
      'id'             => 'user_data',
      'click sortable' => TRUE,
    ],
    'filter'       => [
      'id'          => 'user_name',
      'allow empty' => TRUE,
    ],
    'argument'     => [
      'id' => 'user_uid',
    ],
    'sort'         => [
      'id' => 'standard',
    ],
    'relationship' => [
      'base'       => 'users',
      'base_field' => 'uid',
      'id'         => 'standard',
      'label'      => t('Acceptances to user relationship'),
    ],
  ];

  $data['glegal_accepted']['accepted'] = [
    'title'  => t('Time Accepted'),
    'help'   => t('Time and date user accepted T&Cs.'),
    'field'  => [
      'id'             => 'date',
      'click sortable' => TRUE,
    ],
    'filter' => [
      'id'          => 'date',
      'allow empty' => TRUE,
    ],
    'sort'   => [
      'id' => 'date',
    ],
  ];

  $data['glegal_conditions'] = [
    'version'    => [
      'title'    => t('Version'),
      'help'     => t('Version of T&Cs.'),
      'field'    => [
        'id'             => 'standard',
        'click sortable' => TRUE,
      ],
      'filter'   => [
        'id'          => 'numeric',
        'allow empty' => TRUE,
      ],
      'argument' => [
        'id' => 'numeric',
      ],
      'sort'     => [
        'id' => 'standard',
      ],
    ],
    'revision'   => [
      'title'    => t('Revision'),
      'help'     => t('Revision of T&Cs.'),
      'field'    => [
        'id'             => 'standard',
        'click sortable' => TRUE,
      ],
      'filter'   => [
        'id'          => 'numeric',
        'allow empty' => TRUE,
      ],
      'argument' => [
        'id' => 'numeric',
      ],
      'sort'     => [
        'id' => 'standard',
      ],
    ],
    'conditions' => [
      'title'    => t('Terms & Conditions'),
      'help'     => t('Text of T&Cs.'),
      'field'    => [
        'id'             => 'standard',
        'click sortable' => TRUE,
      ],
      'filter'   => [
        'id'          => 'string',
        'allow empty' => TRUE,
      ],
      'argument' => [
        'id' => 'string',
      ],
      'sort'     => [
        'id' => 'standard',
      ],
    ],
    'date'       => [
      'title'  => t('Date Added'),
      'help'   => t('Date T&Cs where added'),
      'field'  => [
        'id' => 'date',
      ],
      'filter' => [
        'id' => 'date',
      ],
      'sort'   => [
        'id' => 'date',
      ],
    ],
    'changes'    => [
      'title'    => t('Changes'),
      'help'     => t('Explanation of changes to T&Cs'),
      'field'    => [
        'id'             => 'exploded_list',
        'click sortable' => TRUE,
      ],
      'filter'   => [
        'id'          => 'string',
        'allow empty' => TRUE,
      ],
      'argument' => [
        'id' => 'string',
      ],
      'sort'     => [
        'id' => 'standard',
      ],
    ],
    'table'      => [
      'group' => t('glegal'),
      'base'  => [
        'field'  => 'tc_id',
        'title'  => t('glegal terms and conditions'),
        'help'   => 'Versions of the terms and conditions text',
        'weight' => 10,
      ],
    ],
  ];

  if (\Drupal::moduleHandler()->moduleExists('locale')) {

    $data['glegal_conditions']['language'] = [
      'title'    => t('Language'),
      'help'     => t('Language of T&Cs.'),
      'field'    => [
        'id'             => 'language',
        'click sortable' => TRUE,
      ],
      'filter'   => [
        'id'          => 'language',
        'allow empty' => TRUE,
      ],
      'argument' => [
        'id' => 'language',
      ],
      'sort'     => [
        'id' => 'standard',
      ],
    ];
  }

  return $data;
}
