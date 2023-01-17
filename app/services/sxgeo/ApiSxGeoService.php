<?php
namespace App\Services\SxGeo;

define('INFO', true);
set_time_limit(600);
header('Content-type: text/plain; charset=utf8');

// use Exception;
// use GuzzleHttp\Client;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Storage;

class ApiSxGeoService {

    /**
     * API веб-сервиса 
     * 
     * @access protected
     * @var string
     */
    protected $api_endpoint = 'https://sypexgeo.net/files/SxGeoCity_utf8.zip';

    /**
     * Каталог в который сохранять dat-файл
     * 
     * @access protected
     * @var string
     */
    protected $datFileDir = 'sxgeo';

    /**
     * Файл в котором хранится дата последнего обновления
     * 
     * @access protected
     * @var string
     */
    protected $lastUpdatedFile = 'sxgeo/SxGeo.upd';


    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->update();
    }

    /**
     * Обновление файла базы данных Sypex Geo
     * 
     * @access public
     * @return boolean
     */
    public function update(): void
    {
        chdir(storage_path($this->datFileDir));
        $types = array(
            'Country' =>  'SxGeo.dat',
            'City' =>  'SxGeoCity.dat',
            'Max' =>  'SxGeoMax.dat',
        );
        // Скачиваем архив
        preg_match("/(Country|City|Max)/", pathinfo($this->api_endpoint, PATHINFO_BASENAME), $m);
        $type = $m[1];
        $dat_file = $types[$type];
        if (INFO) echo "Скачиваем архив с сервера\n";

        $fp = fopen(storage_path($this->datFileDir) .'/SxGeoTmp.zip', 'wb');
        $ch = curl_init($this->api_endpoint);
        curl_setopt_array($ch, array(
            CURLOPT_FILE => $fp,
            CURLOPT_HTTPHEADER => file_exists(storage_path($this->lastUpdatedFile)) ? array("If-Modified-Since: " .file_get_contents(storage_path($this->lastUpdatedFile))) : array(),
        ));
        if(!curl_exec($ch)) die ('Ошибка при скачивании архива');
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);
        if ($code == 304) {
            @unlink(storage_path($this->datFileDir) . '/SxGeoTmp.zip');
            if (INFO) echo "Архив не обновился, с момента предыдущего скачивания\n";
            exit;
        }

        if (INFO) echo "Архив с сервера скачан\n";
        // Распаковываем архив
        $fp = fopen('zip://' . storage_path($this->datFileDir) . '/SxGeoTmp.zip#' . $dat_file, 'rb');
        $fw = fopen($dat_file, 'wb');
        if (!$fp) {
            exit("Не получается открыть\n");
        }
        if (INFO) echo "Распаковываем архив\n";
        stream_copy_to_stream($fp, $fw);
        fclose($fp);
        fclose($fw);
        if(filesize($dat_file) == 0) die ('Ошибка при распаковке архива');
        @unlink(storage_path($this->datFileDir) . '/SxGeoTmp.zip');
        rename(storage_path($this->datFileDir) . '/' . $dat_file, storage_path($this->datFileDir) . '/' . $dat_file) or die ('Ошибка при переименовании файла');
        file_put_contents(storage_path($this->lastUpdatedFile), gmdate('D, d M Y H:i:s') . ' GMT');
        if (INFO) echo "Перемещен файл в {storage_path($this->datFileDir)}{$dat_file}\n";
    }
}