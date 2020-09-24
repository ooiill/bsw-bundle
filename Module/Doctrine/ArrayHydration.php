<?php

namespace Leon\BswBundle\Module\Doctrine;

use Doctrine\ORM\Internal\Hydration\ArrayHydrator;
use PDO;

/**
 * Class ArrayHydration
 */
class ArrayHydration extends ArrayHydrator
{
    /**
     * @const string
     */
    const HYDRATE_ARRAY = 'bsw_hydrate_array';

    /**
     * @return array
     * @throws
     */
    protected function hydrateAllData()
    {
        $result = [];

        while ($data = $this->_stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->hydrateRowData($data, $result);
        }

        $resultHanding = [];
        foreach ($result as $index => $item) {
            foreach ($item as $key => $value) {
                if (is_scalar($value)) {
                    $resultHanding[$index][$key] = $value;
                } elseif (is_array($value)) {
                    $resultHanding[$index] = array_merge($resultHanding[$index] ?? [], $value);
                }
            }
        }

        return $resultHanding;
    }
}