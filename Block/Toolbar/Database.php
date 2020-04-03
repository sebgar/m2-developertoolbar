<?php
namespace Sga\DeveloperToolbar\Block\Toolbar;

use Sga\DeveloperToolbar\Block\Toolbar\AbstractBlock;

class Database extends AbstractBlock
{
    protected $_queries;
    protected $_multiples;

    public $max = 0;
    public $nbUnique = 0;
    public $nbMultiple = 0;

    public function getCode()
    {
        return 'database';
    }

    public function getLabel()
    {
        return 'Database';
    }

    public function getQueries()
    {
        if (!isset($this->_queries)) {
            $profiler = $this->_resource->getConnection('read')->getProfiler();
            $queries = $profiler->getQueryProfiles();

            $this->_queries = [];
            if (is_array($queries)) {
                $this->max = count($queries);

                foreach ($queries as $query) {
                    $realQuery = $this->getRealQuery($query);
                    $key = md5($realQuery);

                    if (!isset($this->_queries[$key])) {
                        $this->_queries[$key] = [
                            'nb' => 1,
                            'sql' => $realQuery,
                            'time' => $query->getElapsedSecs()
                        ];

                        $this->nbUnique++;
                    } else {
                        $this->_queries[$key]['nb']++;
                        $this->_queries[$key]['time'] += $query->getElapsedSecs();

                        $this->nbMultiple++;
                    }
                }

                // delete query with nb = 1
                $toSort = [];
                foreach ($this->_queries as $k => $data) {
                    if ($data['nb'] > 1) {
                        $toSort[$k] = $data['nb'];
                    }
                }

                // sort queries by nb desc
                arsort($toSort);

                // make final list
                $this->_multiples = [];
                foreach ($toSort as $k => $nb) {
                    $this->_multiples[] = $this->_queries[$k];
                }
            }
        }

        return $this->_queries;
    }

    public function getMultipleQueries()
    {
        if (!isset($this->_multiples)) {
            $this->getQueries();
        }
        return $this->_multiples;
    }

    public function getRealQuery($query)
    {
        $str = $query->getQuery();
        if (count($query->getQueryParams()) > 0) {
            foreach ($query->getQueryParams() as $k => $v) {
                $str = str_replace($k, $v, $str);
            }
        }

        return $str;
    }

    public function formatSql($sql)
    {
        $htmlSql = $sql;
        $htmlSql = preg_replace('/\b(SET|AS|ASC|COUNT|DESC|IN|LIKE|DISTINCT|INTO|VALUES|LIMIT)\b/', '<span class="sqlword">\\1</span>', $sql);
        $htmlSql = preg_replace('/\b(UNION ALL|DESCRIBE|SHOW|connect|begin|commit)\b/', '<br/><span class="sqlother">\\1</span>', $htmlSql);
        $htmlSql = preg_replace('/\b(UPDATE|SELECT|FROM|WHERE|LEFT JOIN|INNER JOIN|RIGHT JOIN|ORDER BY|GROUP BY|DELETE|INSERT)\b/', '<br/><span class="sqlmain">\\1</span>', $htmlSql);
        $htmlSql = preg_replace('/^<br\/>/', '', $htmlSql);
        return $htmlSql;
    }
}
