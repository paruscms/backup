<?php

namespace Parus\Backup;

class Backup
{

    private const ADD = 'A';
    private const EDIT = 'E';
    private const REPLACE = 'R';
    private const DELETE= 'D';

    public function __construct($table = null, $type = null, $result = null, $tmp = null)
    {
        $this->table = $table ?? '';
        $this->type = $type ?? '';
        $this->tmp = $tmp ?? '';
        $this->id = $result ?? 0;
        $this->screen = $this->screen();
    }

    /**
     * @return void
     */

    public function create(): void
    {
        $db = Database::getInstance();
        $data = $this->data();
        $db::insert('backup', $data);
    }

    /**
     * @return array
     */

    public function data(): array
    {
        $user = Users::info();
        $data = array(
            'user_id' => $user['id'],
            'title' => $this->getTitle(),
            'type' => $this->type,
            'screen' => $this->screen,
            'object' => $this->table,
            'object_id' => $this->id,
            'before_to' => $this->before() ?? '',
            'time' => $time
        );

        if (!empty($this->tmp)) {
            $this->create_data();
        }

        return $data;
    }

    public function create_data(): void
    {

        $str = serialize($this->tmp);
        $dir = H . 'uploads/backup/';
        if (!is_dir($dir)) {
            mkdir($dir);
            @chmod($dir, 0777);
        }
        $file = $dir . $this->screen . '.dat';
        file_put_contents($file, $str);
        @chmod($file, 0777);

    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        $type = $this->type;
        if ($type == self::ADD) {
            $title = 'Добавление';
        } elseif ($type == self::EDIT) {
            $title = 'Редактирование';
        } elseif ($type == self::DELETE) {
            $title = 'Удаление';
        } elseif ($type == self::REPLACE) {
            $title = 'Замена';
        }
        return $title ?? 'Прочие действия';
    }

    /**
     * @return array|string|string[]
     */
    public function screen(): array|string
    {
        return substr_replace(sha1(microtime(true)), '', 12);
    }

    public function before()
    {
        $db = Database::getInstance();
        $select = [
            $this->table => [
                'id' => $this->id
            ]
        ];
        return $db->select($select);
    }

}
