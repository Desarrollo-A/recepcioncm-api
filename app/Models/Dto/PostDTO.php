<?php 
    namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

    class PostDTO
    {
        use DataTransferObject;

        /**
         * @var integer
         */

         public $id;

         /**
          * @var string
          */
        public $title;
        /**
         * @var string
         */
        public $body;

         /**
         * @throws CustomErrorException
         */
        public function __construct(array $data = [])
        {
            if (count($data) > 0) {
                $this->init($data);
            }
        }
    }
    
?>