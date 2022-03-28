<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class options extends Command
{
    public $migrationContent = '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\'options\', function (Blueprint $table) {
            $table->id();
            $table->string(\'key\');
            $table->text(\'value\');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(\'options\');
    }
}
    ';


    public $modelContent = '<?php

    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class Option extends Model
    {
        use HasFactory;
    
        protected $fillable = [\'key\', \'value\'];
    }
    ';

    public $helperContent = '<?php
    namespace App\Helpers {
    
        use App\Models\Option;
    
        class Options
        {
            private static $options = [];
    
            public static function getOption ( $key, $default = NULL )
            {
                if ( empty( self::$options ) )
                {
                    $options = Option::all();
    
                    foreach ( $options as $option )
                    {
                        self::$options[ $option->key ] = $option->value;
                    }
                }
    
                return array_key_exists( $key, self::$options ) ? self::$options[ $key ] : $default;
            }
        }
    }
    ';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'options:create {arguments*}';

    /**
     * The console command description.
     * php artisan options:create 'label1*name1' 'label2*name2' kimi çalışdırılır 
     * // output : array:2 [
     *  0 => "label1*name1"
     *  1 => "label2*name2"
     *  ]
     *
     * @var string
     */
    protected $description = 'Options created';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $arguments = $this->argument('arguments');
        $this->createFile($this->migrationContent, base_path().'/database/migrations/','2022_03_05_040356_create_options_table.php');
        $this->createFile($this->modelContent, base_path().'/app/Models/','Option.php');
        $this->createFile($this->helperContent, base_path().'/app/Helpers/','Options.php');
    }

    public function createFile($data, $destinationPath, $file)
    {
        if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
        File::put($destinationPath.$file,$data);
        return response()->download($destinationPath.$file);
    }
}
