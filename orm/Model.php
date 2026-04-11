<?php

class Model
{
    protected $data = [];

    public function create()
    {
        $table = get_class($this);

        $columns = array_keys($this->data);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);

        $sql = "INSERT INTO `$table` (" . implode(', ', $columns) . ")
            VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = DB::$db->prepare($sql);

        foreach ($this->data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}