<?php
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::truncate();

        $data = [];

        array_push($data, [
            'name' => 'admin',
            'email' => 'test@lumen.com',
            'password' => Hash::make('testlumen')
        ]);

        User::insert($data);
    }
}
