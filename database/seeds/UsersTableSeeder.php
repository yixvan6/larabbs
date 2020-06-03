<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Arr;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $avatars = [
            'https://cdn.learnku.com/uploads/images/201710/14/1/s5ehp11z6s.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/Lhd1SHqu86.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/LOnMrqbHJn.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/xAuDMxteQy.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/ZqM7iaP4CR.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/NDnzMutoxX.png',
        ];

        $users = factory(User::class, 10)->make()->each(function ($user) use ($avatars) {
            $user->avatar = Arr::random($avatars);
        });

        // 让隐藏字段可见，并将数据集合转换为数组
        $data = $users->makeVisible(['password', 'remember_token'])->toArray();

        User::insert($data);

        // 单独处理 1,2 号用户，并赋予角色
        $user = User::find(1);
        $user->name = 'yixvan6';
        $user->email = 'yixvan6@163.com';
        $user->save();
        $user->assignRole('founder');

        $user = User::find(2);
        $user->assignRole('maintainer');
    }
}
