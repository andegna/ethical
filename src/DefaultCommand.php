<?php

namespace Andegna\Ethical;


use Andegna\Constants;
use Andegna\DateTimeFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ethical')
            ->setDescription('displays a calendar and the date of Easter')
            ->setHelp('displays a calendar and the date of Easter')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('highlighting', 't', InputOption::VALUE_OPTIONAL, 'Turns off highlighting of today',
                        true),
                    new InputOption('month', 'm', InputOption::VALUE_OPTIONAL, 'Display the specified month.', null),
                    new InputOption('year', 'y', InputOption::VALUE_OPTIONAL, 'the year', null),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = DateTimeFactory::now();

        [$day, $month, $year] = $this->dates($input, $now);

        $highlighting = $this->needsHighlighting($input, $now, $month, $year);

        $firstDay = $this->getTheFirstDayOfTheMonth($year, $month);

        $days_array = $this->fetchDaysArray($day, $highlighting, $firstDay);

        $monthTableHeader = $firstDay->format('F Y');
        $monthTableHeader = str_repeat(' ', (20 - mb_strlen($monthTableHeader)) / 2) . $monthTableHeader;

        $this->generateTable($output, $monthTableHeader, $days_array)
            ->render();
    }


    protected function needsHighlighting(InputInterface $input, $now, $month, $year): bool
    {
        return (int)$month === $now->getMonth() &&
            (int)$year === $now->getYear() &&
            $input->getOption('highlighting');
    }

    protected function fetchDaysArray($day, $high, $time): array
    {
        $days = array_map(function($n) use ($day, $high) {
            if ($n === $day && $high) {
                return sprintf('<comment>%2d</comment>', $n);
            }

            if ($n >= 1) {
                return sprintf('%2d', $n);
            }

            return '';
        }, range(2 - $time->getDayOfWeek(), $time->getDaysInMonth()));

        return $days;
    }

    protected function dates(InputInterface $input, $now): array
    {
        $day = $now->getDay();
        $month = $input->getOption('month') ?? $now->getMonth();
        $year = $input->getOption('year') ?? $now->getYear();

        return [$day, $month, $year];
    }

    protected function generateTable(OutputInterface $output, $date, $days): Table
    {
        $table = (new Table($output))
            ->setHeaders([
                [new TableCell($date, ['colspan' => 7])],
                array_map(function($e) {
                    return mb_substr($e, 0, 2);
                }, Constants::WEEK_NAME),
            ])
            ->setRows(array_chunk($days, 7));

        $table->setStyle('compact');

        return $table;
    }

    /**
     * @param $year
     * @param $month
     * @return \Andegna\DateTime
     * @throws \Andegna\Exception\InvalidDateException
     */
    protected function getTheFirstDayOfTheMonth($year, $month): \Andegna\DateTime
    {
        return DateTimeFactory::of($year, $month, 1, 8, 9, 10);
    }

}
