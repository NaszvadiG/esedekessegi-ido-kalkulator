<?php
use PHPUnit\Framework\TestCase;

require_once './app/DueDateCalculator.php';

class DueDateCalculatorTest extends TestCase {

	public function testCalculateDueDate() {
		// Teszt esetek
		$testCases = [
			['2023-08-28 14:12:00', 16, '2023-08-30 14:12:00'],
			['2023-08-29 09:12:00', 8, '2023-08-30 09:12:00'],
			['2023-08-29 09:12:00', 8, '2023-08-30 11:12:00'],		// szándékosan rossz teszt eset !
		];

		foreach ($testCases as $testCase) {
			list($submitTime, $turnaroundHours, $expectedDueDate) = $testCase;

			$dueDate = DueDateCalculator::calculateDueDate($submitTime, $turnaroundHours);
			$this->assertEquals($expectedDueDate, $dueDate);
		}
	}
}
?>
