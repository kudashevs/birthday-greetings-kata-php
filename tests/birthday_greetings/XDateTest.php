<?php

namespace birthday_greetings;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class XDateTest extends TestCase
{
    #[Test]
    public function it_can_parse_a_date(): void
    {
        $date = new XDate("1989/01/14");
        $this->assertEquals(14, $date->getDay());
        $this->assertEquals(1, $date->getMonth());
    }

    #[Test]
    public function it_can_identify_same_dates(): void
    {
        $date = new XDate('1989/01/14');
        $sameDay = new XDate('2001/01/14');
        $differentDay = new XDate('1989/01/15');
        $differentMonth = new XDate('1989/02/14');

        // @note remove different
        $this->assertTrue($date->isSameDay($sameDay));
        $this->assertFalse($date->isSameDay($differentDay));
        $this->assertFalse($date->isSameDay($differentMonth));
    }

    #[Test]
    public function it_can_identify_different_dates(): void
    {
        $date = new XDate('1989/01/14');
        $differentDay = new XDate('1989/01/15');
        $differentMonth = new XDate('1989/02/14');

        $this->assertFalse($date->isSameDay($differentDay));
        $this->assertFalse($date->isSameDay($differentMonth));
    }
} 