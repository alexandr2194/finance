<?php
namespace Finance\Models;

use Finance\Core\Database;
use Finance\Core\Model;

/**
 * Class MainModel
 *
 * @package Finance\Models
 */
class IndexModel extends Model
{
    /**
     * @param array $session
     * @param array $server
     * @return $this
     */
    public function buildData(array $session, array $server)
    {
        $this->data['isLogin'] = $this->isUserLoginIn($session);
        if ($this->data['isLogin']) {
            $currentUser = Database::getInstance()->query(
                "SELECT * FROM user WHERE id = " . $session['user_id']
            );
            $this->data['userName'] = $currentUser[0]['name'];
        }

        /**
         * Home slider
         */

        $sliders = Database::getInstance()->query(
            "SELECT * FROM home_slider WHERE is_active = 1"
        );
        foreach ($sliders as $slider) {
            $this->data['sliders'][] = [
                'media' => $this->getImage($slider['media_id']),
                'title' => $slider['title'],
                'description' => $slider['description']
            ];
        }

        /**
         * Goods
         */
        switch ($server['REQUEST_URI']) {
            case '/goods/male':
                $goodsQuery = "SELECT * FROM item WHERE is_deleted = 0 AND category = 1 ORDER BY number_of_sales";
                break;
            case '/goods/female':
                $goodsQuery = "SELECT * FROM item WHERE is_deleted = 0 AND category = 2 ORDER BY number_of_sales";
                break;
            default:
                $goodsQuery = "SELECT * FROM item WHERE is_deleted = 0 ORDER BY number_of_sales";
        }
        $goods = Database::getInstance()->query($goodsQuery);
        foreach ($goods as $good) {
            $this->data['goods'][] = [
                'id' => $good['id'],
                'media' => $this->getImage($good['media_id']),
                'title' => $good['title'],
                'price' => $good['price']
            ];
        }

        return $this;
    }

    public function getImage($mediaId)
    {
        $media = Database::getInstance()->query("SELECT * FROM media WHERE id = {$mediaId}");
        if ($media) {
            return 'data:image/*;base64,' . $media[0]['media'];
        } else {
            return 'data:image/*;base64,' . base64_encode(file_get_contents(__DIR__ . '/../../public/img/no_image.png.jpg'));
        }
    }

    /**
     * @param $session
     * @return bool
     */
    private function isUserLoginIn(array $session): bool
    {
        return isset($session['login']);
    }
}