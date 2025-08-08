<?php

namespace BirthdayGreetings\Tests;

use BirthdayGreetings\Employee;
use BirthdayGreetings\XDate;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EmployeeTest extends TestCase
{
    #[Test]
    public function it_can_identify_a_birthday_day(): void
    {
        $employee = new Employee('foo', 'bar', '1990/01/31', 'a@b.c');
        $this->assertTrue($employee->isBirthday(new XDate('2008/01/31')));
        $this->assertFalse($employee->isBirthday(new XDate('2008/01/30')));
        $this->assertFalse($employee->isBirthday(new XDate('2008/02/01')));
    }
} 