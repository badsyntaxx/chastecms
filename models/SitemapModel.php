<?php 

/**
 * Sitemap Model Class
 *
 * Interact with the database to process data related to pages.
 */
class SitemapModel extends Model
{
    public function getSitelinks()
    {
        // SELECT * FROM `sitemap` ORDER BY `sort_order` ASC
        $select = $this->table('sitemap')->select('*')->orderBy('sort_order', 'asc')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getVisibleSitelinks()
    {
        // SELECT * FROM `sitemap` ORDER BY `link_order` ASC
        $select = $this->table('sitemap')->select('*')->orderBy('sort_order', 'asc')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                if (!empty($select['response'])) {
                    $links = $select['response'];
                    foreach ($links as $link) {
                        if ($link['visibility'] == 0) {
                            $sitelinks[] = $link;
                        }
                    }
                    return $sitelinks;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function getSitelink($where, $param)
    {
        // SELECT * FROM `sitemap` WHERE `name` = "home" LIMIT 1
        $select = $this->table('sitemap')->select('*')->where($where, $param)->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }
    
    public function eraseSitemap()
    {
        // TRUNCATE TABLE `sitemap`
        if ($this->table('pages')->truncate('sitemap')) {
            return true;
        } else {
            return false;
        }
    }

    public function insertSitelink($data)
    {
        // INSERT INTO `sitemap` (`name`, `route_name`, `route`, `sort_order`) VALUES (?, ?, ?, ?)
        $insert = $this->table('sitemap')->insert($data)->execute($data);
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    public function updateSitemap($where, $data)
    {
        // UPDATE `sitemap` SET `value` = ? WHERE `name` = ?
        $update = $this->table('sitemap')->update($data)->where($where)->execute();
        if ($update) {
            if ($update['status'] == 'success') {
                if ($update['affected_rows'] > 0) {
                    return empty($update['response']) ? true : $update['response'];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}