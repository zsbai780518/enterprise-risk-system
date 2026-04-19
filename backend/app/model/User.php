<?php
// +----------------------------------------------------------------------
// | 企业风险分析系统 - 用户模型
// +----------------------------------------------------------------------

namespace app\model;

use think\Model;

class User extends Model
{
    // 定义数据表（含前缀）
    protected $table = 'ers_user';
    
    // 定义主键
    protected $pk = 'id';
    
    // 定义时间字段名称
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
    // 隐藏字段
    protected $hidden = ['password'];
    
    // 类型转换
    protected $type = [
        'id' => 'integer',
        'role' => 'integer',
        'status' => 'integer',
    ];
    
    /**
     * 密码加密
     * @param string $password 明文密码
     * @return string 加密后的密码
     */
    public static function encryptPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * 验证密码
     * @param string $password 明文密码
     * @param string $hashedPassword 加密后的密码
     * @return bool
     */
    public static function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }
    
    /**
     * 用户注册
     * @param array $data 注册数据
     * @return array ['code'=>状态码，'msg'=>消息，'data'=>用户数据]
     */
    public static function register(array $data): array
    {
        // 检查手机号是否已存在
        if (!empty($data['phone'])) {
            $exist = self::where('phone', $data['phone'])->find();
            if ($exist) {
                return ['code' => 400, 'msg' => '手机号已注册'];
            }
        }
        
        // 检查用户名是否已存在
        if (!empty($data['username'])) {
            $exist = self::where('username', $data['username'])->find();
            if ($exist) {
                return ['code' => 400, 'msg' => '用户名已存在'];
            }
        }
        
        // 加密密码
        $data['password'] = self::encryptPassword($data['password']);
        $data['role'] = 1; // 默认普通用户
        $data['status'] = 1; // 默认正常
        
        // 创建用户
        $user = self::create($data);
        if (!$user) {
            return ['code' => 500, 'msg' => '注册失败'];
        }
        
        // 返回用户信息（不含密码）
        unset($data['password']);
        return ['code' => 200, 'msg' => '注册成功', 'data' => $user];
    }
    
    /**
     * 用户登录
     * @param string $account 账号（手机号/用户名）
     * @param string $password 密码
     * @return array
     */
    public static function login(string $account, string $password): array
    {
        // 查询用户
        $where = [];
        if (preg_match('/^1[3-9]\d{9}$/', $account)) {
            $where['phone'] = $account;
        } else {
            $where['username'] = $account;
        }
        
        $user = self::where($where)->find();
        if (!$user) {
            return ['code' => 401, 'msg' => '用户不存在'];
        }
        
        // 验证密码
        if (!self::verifyPassword($password, $user->password)) {
            return ['code' => 401, 'msg' => '密码错误'];
        }
        
        // 检查状态
        if ($user->status != 1) {
            return ['code' => 403, 'msg' => '账号已被禁用'];
        }
        
        // 更新最后登录时间
        $user->last_login_at = date('Y-m-d H:i:s');
        $user->save();
        
        // 生成 token（简单实现，生产环境建议使用 JWT）
        $token = md5(uniqid() . time() . $user->id);
        
        return [
            'code' => 200,
            'msg' => '登录成功',
            'data' => [
                'user_id' => $user->id,
                'username' => $user->username,
                'phone' => $user->phone,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'token' => $token,
                'expires_in' => 86400 * 7 // 7 天有效期
            ]
        ];
    }
    
    /**
     * 获取用户绑定的企业列表
     * @param int $userId 用户 ID
     * @return array
     */
    public function getBoundCompanies(int $userId): array
    {
        $companies = db('ers_user_company')
            ->alias('uc')
            ->join('ers_company c', 'uc.company_id = c.id')
            ->where('uc.user_id', $userId)
            ->field('c.*, uc.is_owner')
            ->select()
            ->toArray();
        
        return $companies;
    }
}
