<?php

class Stripe_UtilTest extends UnitTestCase
{
  public function testIsList()
  {
    $list = array(5, 'nstaoush', array());
    $this->assertTrue(Stripe_Util::isList($list));

    $notlist = array(5, 'nstaoush', array(), 'bar' => 'baz');
    $this->assertFalse(Stripe_Util::isList($notlist));
  }

  public function testArrayClone()
  {
    try {
      Stripe_Util::arrayClone(1);
      $this->assertFalse(true);
    } catch (Stripe_Error $e) {
      $this->assertTrue(true);
    }
  }
}
