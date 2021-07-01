<?php

namespace Leon\BswBundle\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

class BswSkinCommand extends LogicHandlerCommand
{
    /**
     * @return array
     */
    public function args(): array
    {
        return array_merge(
            parent::args(),
            [
                'skin' => [null, InputOption::VALUE_OPTIONAL, 'Skin name'],
            ]
        );
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'backend-skin',
            'info'    => 'Change skin for backend',
        ];
    }

    /**
     * @return bool
     */
    public function pass(): bool
    {
        return true;
    }

    /**
     * Logic
     */
    public function handler()
    {
        $project = $this->kernel->getProjectDir();
        $dumper = new Dumper();

        /*
         * twig.yaml
         */

        $twigFile = "{$project}/config/packages/twig.yaml";
        $twig = Yaml::parseFile($twigFile, Yaml::PARSE_CONSTANT);

        // reset skin
        foreach ($twig['twig']['paths'] as $key => $val) {
            if (strpos($val, '%kernel.project_dir%/templates-') === 0) {
                unset($twig['twig']['paths'][$key]);
                $twig['twig']['paths'] = array_values($twig['twig']['paths']);
            }
        }

        if (!empty($this->params->skin)) {
            array_unshift($twig['twig']['paths'], "%kernel.project_dir%/templates-{$this->params->skin}");
        }

        file_put_contents($twigFile, $dumper->dump($twig, 4, 0, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE));
        $this->output->writeln("<info> Change yaml -> {$twigFile} </info>");

        /*
         * services.yaml
         */

        $servicesFile = "{$project}/config/services.yaml";
        $services = Yaml::parseFile($servicesFile, Yaml::PARSE_CONSTANT);
        $services['parameters']['skin'] = null;

        if (!empty($this->params->skin)) {
            $services['parameters']['skin'] = $this->params->skin;
        }

        file_put_contents($servicesFile, $dumper->dump($services, 4, 0, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE));
        $this->output->writeln("<info> Change yaml -> {$servicesFile} </info>");
    }
}