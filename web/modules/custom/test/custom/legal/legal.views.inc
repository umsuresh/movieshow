<?php

/**
 * @file
 * Views handler and plugin definitions for the legal module.
 */

/**
 * Implements hook_views_data().
 */
function legal_views_data() {
  $data                                     = [];
  $data['legal_accepted']['table']['group'] = t('Legal');

  $data['legal_accepted']['table']['join']['users'] = [
    'left_field' => 'uid',
    'field'      => 'uid',
  ];

  $data['users']['legal_accepted_rel'] = [
    'real field'   => 'uid',
    'title'        => t('accepted user'),
    'group'        => t('Legal'),
    'help'         => t('An accepted user'),
    'relationship' => [
      'id'                 => 'standard',
      'base'               => 'legal_accepted',
      'base field'         => 'uid',
      'label'              => t('Legal'),
      'relationship field' => 'uid',
    ],
  ];

  $data['legal_accepted']['legal_conditions_rel'] = [
    'group'        => t('Legal'),
    'title'        => t('Legal Conditions relationship'),
    'help'         => t('Create a relationship to a legal conditions and legal accepted.'),
    'relationship' => [
      'id'                 => 'standard',
      'base'               => 'legal_conditions',
      'base field'         => 'tc_id',
      'label'              => t('Legal'),
      'relationship field' => 'tc_id',
    ],
  ];

  $data['legal_accepted']['version'] = [
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

  $data['legal_accepted']['revision'] = [
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
    $data['legal_accepted']['language'] = [
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

  $data['legal_accepted']['uid'] = [
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

  $data['legal_accepted']['accepted'] = [
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

  $data['legal_conditions'] = [
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
    'extras'     => [
      'title'    => t('Additional Checkboxes'),
      'help'     => t('Additional terms of T&Cs'),
      'field'    => [
        'id' => 'unserialized_list',
      ],
      'filter'   => [
        'id' => 'string',
      ],
      'argument' => [
        'id' => 'string',
      ],
      'sort'     => [
        'id' => 'standard',
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
      'group' => t('Legal'),
      'base'  => [
        'field'  => 'tc_id',
        'title'  => t('Legal terms and conditions'),
        'help'   => 'Versions of the terms and conditions text',
        'weight' => 10,
      ],
    ],
  ];

  if (\Drupal::moduleHandler()->moduleExists('locale')) {

    $data['legal_conditions']['language'] = [
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
