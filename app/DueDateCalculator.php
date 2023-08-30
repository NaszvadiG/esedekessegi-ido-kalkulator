<?php
class DueDateCalculator
{
	const WORK_START_HOUR = 9;
	const WORK_END_HOUR = 17;
	const WORK_WEEKDAYS = [1, 2, 3, 4, 5]; // Hétfőtől péntekig

	public static function calculateDueDate($submitTime, $turnaroundHours)
	{
		$submitDateTime = new DateTime($submitTime);

		// Először korrigáljuk a beküldési időt a munkaidőhöz
		$submitDateTime = self::adjustToWorkHours($submitDateTime);

		// Kiszámítjuk a munkanapok és órák számát
		$workDays = intdiv($turnaroundHours, (self::WORK_END_HOUR - self::WORK_START_HOUR));
		$remainingHours = $turnaroundHours % (self::WORK_END_HOUR - self::WORK_START_HOUR);

		// Számoljuk a végleges esedékességi dátumot
		$dueDateTime = clone $submitDateTime;
		$dueDateTime = self::addWorkDays($dueDateTime, $workDays);

		// Hozzáadjuk a maradék órákat és figyeljük a munkaidő határokat
		$dueDateTime->modify('+' . $remainingHours . ' hours');
		$dueDateTime = self::adjustToWorkHours($dueDateTime);

		return $dueDateTime->format('Y-m-d H:i:s');
	}

	private static function adjustToWorkHours($dateTime)
	{
		$dayOfWeek = $dateTime->format('N');

		if ($dayOfWeek == 6 || $dayOfWeek == 7 || ($dayOfWeek == 5 && $dateTime->format('H') >= self::WORK_END_HOUR)) {
			$dateTime->modify('next ' . self::WORK_WEEKDAYS[0] . ' ' . self::WORK_START_HOUR . ':00:00');
		}
		elseif ($dateTime->format('H') < self::WORK_START_HOUR) {
			$dateTime->setTime(self::WORK_START_HOUR, 0, 0);
		}
		elseif ($dateTime->format('H') >= self::WORK_END_HOUR || ($dayOfWeek == 5 && $dateTime->format('H') >= self::WORK_END_HOUR)) {
			$dateTime->modify('next ' . self::WORK_WEEKDAYS[($dayOfWeek % 5)] . ' ' . self::WORK_START_HOUR . ':00:00');
		}

		return $dateTime;
	}

	private static function addWorkDays($dateTime, $days)
	{
		for ($i = 0; $i < $days; $i++) {
			$dateTime->modify('+1 day');
			while (!in_array($dateTime->format('N'), self::WORK_WEEKDAYS)) {
				$dateTime->modify('+1 day');
			}
		}
		return $dateTime;
	}
}
?>
