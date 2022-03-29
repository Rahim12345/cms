<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class options extends Command
{
    private $arguments = [
        [
            'label' => 'Facebook',
            'name' => 'facebook'
        ],
        [
            'label' => 'Twitter',
            'name' => 'twitter'
        ],
        [
            'label'=>'YouTube',
            'name'=>'youtube'
        ],
        [
            'label'=>'Email',
            'name'=>'email'
        ],
        [
            'label'=>'Telefon',
            'name'=>'tel'
        ]
    ];

    private $StoreOptionRequest;

    private $optionsBlade;

    private $migrationContent = '<?php

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


    private $modelContent = '<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Option extends Model
    {
        use HasFactory;

        protected $fillable = [\'key\', \'value\'];
    }
    ';

    private $helperContent = '<?php
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

    private $OptionControllerContent;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'options:create';

    /**
     * The console command description.
     * php artisan options:create kimi çalışdırılır
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
        $this->optionsBlade = '@extends(\'back.layout.master\')

@section(\'meta\')

@endsection

@section(\'title\') Options @endsection

@section(\'css\')

@endsection

@section(\'content\')
    <div class="page">
        @include(\'back.includes.menu\')

        <div class="content">
            <div class="mb-3 col-md-8 offset-md-2">
                <form action="{{ route(\'option.store\') }}" method="POST">
                    @csrf
                    '.$this->prepareOptionsBlade().'
                    <div class="form-group">
                        <button class="btn btn-primary float-end" type="submit">Əlavə et</button>
                    </div>
                </form>
            </div>
        </div>
        @include(\'back.includes.footer\')
    </div>
@endsection

@section(\'js\')

@endsection

    ';
        $this->OptionControllerContent = '<?php

namespace App\Http\Controllers;

use App\Helpers\Options;
use App\Models\Option;
use App\Http\Requests\StoreOptionRequest;
use App\Http\Requests\UpdateOptionRequest;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(\'back.pages.options\',[
            '.$this->prepareElements().'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOptionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOptionRequest $request)
    {
        $check_add = '.$this->checkForStore().' == \'\' ? true : false;
        foreach ($request->keys() as $key)
        {
            if ($key != \'_token\')
            {
                Option::updateOrCreate(
                    [\'key\'   => $key],
                    [
                        \'value\' => $request->post($key)
                    ]
                );
            }
        }

        if ($check_add)
        {
            toastr()->success(\'Data uğurla əlavə edildi\');
        }
        else
        {
            toastr()->success(\'Data uğurla yeniləndi\');
        }

        return redirect()->route(\'option.index\');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Option  $option
     * @return \Illuminate\Http\Response
     */
    public function show(Option $option)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Option  $option
     * @return \Illuminate\Http\Response
     */
    public function edit(Option $option)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOptionRequest  $request
     * @param  \App\Models\Option  $option
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOptionRequest $request, Option $option)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Option  $option
     * @return \Illuminate\Http\Response
     */
    public function destroy(Option $option)
    {
        //
    }
}
    ';

        $this->StoreOptionRequest = '<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            '.$this->prepareStoreOptionRequestRules().'
        ];
    }

    public function attributes()
    {
        return [
            '.$this->prepareStoreOptionRequestAttributes().'
        ];
    }
}


        ';

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->createFile($this->migrationContent, base_path().'/database/migrations/','2022_03_05_040356_create_options_table.php');
        $this->createFile($this->modelContent, base_path().'/app/Models/','Option.php');
        $this->createFile($this->helperContent, base_path().'/app/Helpers/','Options.php');
        $this->createFile($this->OptionControllerContent, base_path().'/app/Http/Controllers/','OptionController.php');
        $this->createFile($this->StoreOptionRequest, base_path().'/app/Http/Requests/','StoreOptionRequest.php');
        $this->createFile($this->optionsBlade, base_path().'/resources/views/back/pages/','options.blade.php');
        dd("/database/migrations/2022_03_05_040356_create_options_table.php created \n /app/Models/Option.php created \n /app/Helpers/Options.php created \n /app/Http/Controllers/OptionController.php created \n /app/Http/Requests/StoreOptionRequest.php created \n /resources/views/back/pages/options.blade.php");
    }

    public function createFile($data, $destinationPath, $file)
    {
        if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
        File::put($destinationPath.$file,$data);
        return response()->download($destinationPath.$file);
    }

    public function prepareElements()
    {
        $output = '';
        $arguments = $this->arguments;
        foreach ($arguments as $argument)
        {
            $output .= '\''.$argument['name'].'\'=>Options::getOption(\''.$argument['name'].'\'),
            ';
        }

        return $output;
    }

    public function checkForStore()
    {
        $output = '';
        $arguments = $this->arguments;
        $output .= 'Options::getOption(\''.$arguments[0]['name'].'\')';
        return $output;
    }

    public function prepareStoreOptionRequestRules()
    {
        $output = '';
        $arguments = $this->arguments;
        foreach ($arguments as $argument)
        {
            $output .= '\''.$argument['name'].'\'=>\'required|max:255\',
            ';
        }

        return $output;
    }

    public function prepareStoreOptionRequestAttributes()
    {
        $output = '';
        $arguments = $this->arguments;
        foreach ($arguments as $argument)
        {
            $output .= '\''.$argument['name'].'\'=>\''.$argument['label'].'\',
            ';
        }

        return $output;
    }

    public function prepareOptionsBlade()
    {
        $output = '';
        $arguments = $this->arguments;
        foreach ($arguments as $argument)
        {
            $output .= '<div class="form-group mb-3">
                        <label class="form-label" for="'.$argument['name'].'">'.$argument['label'].'</label>
                        <input type="text" class="form-control @error(\''.$argument['name'].'\') is-invalid  @enderror" id="'.$argument['name'].'" name="'.$argument['name'].'" value="{{ old(\''.$argument['name'].'\',$'.$argument['name'].') }}">
                        @error(\''.$argument['name'].'\')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
            ';
        }

        return $output;
    }
}
