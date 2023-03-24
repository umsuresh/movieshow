<?php

namespace Drupal\movie_custom;

use Symfony\Component\EventDispatcher\Event;

class SampleEvent extends Event {

  const SUBMIT = 'movie_custom.submit';
  
  protected $referenceID;

  public function __construct($referenceID)
  {
    $this->referenceID = $referenceID;
  }

  public function getReferenceID()
  {
    return $this->referenceID;
  }

  public function myEventDescription() {
    return "This is as a sample event";
  }

}

