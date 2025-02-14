<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Image;
use think\facade\Filesystem;
use app\common\validate\Avatar as AvatarValidate;
use app\common\exception\ValidateException;

class Upload
{
    // 上传图片类型
    // 用户头像
    public const TYPE_AVATAR = 'avatar';
    // 话题正文
    public const TYPE_CONTENT = 'content';

    // 上传图片压缩宽度
    // 用户头像
    public const MAX_WIDTH_AVATAR = 416;
    // 话题正文
    public const MAX_WIDTH_CONTENT = 1024;

    /**
    * 保存上传图片
    * @Author   zhanghong(Laifuzi)
    * @param    File               $file         文件信息
    * @param    string             $type         文件类型
    * @param    int                $max_width    最大宽度
    * @return   array
    */
    static public function saveImage($file, string $type, int $max_width = 0): array
    {
        if ($type == static::TYPE_AVATAR) {
            $validate = new AvatarValidate;
            if (!$validate->batch(true)->check(['file' => $file])) {
                $e = new ValidateException('上传图片失败');
                $e->setData($validate->getError());
                throw $e;
            }
        }


        // 所有上传文件都保存在项目 public/storage/uploads 目录里
        $save_name = Filesystem::disk('public')->putFile('uploads', $file, 'md5');

        $save_path = '/storage/'.$save_name;
        if ($max_width > 0) {
            //对图片进行等比缩小裁剪，并直接覆盖原图
            $image = Image::open('.'.$save_path);
            $image->thumb($max_width, $max_width)->save('.'.$save_path);
        }

        return [
            'ext' => $file->extension(),
            // 文件实际存储在 public/storage 目录里
            'save_path' => $save_path,
            'sha1' => $file->hash("sha1"),
            'md5' => $file->hash("md5"),
            'size' => $file->getSize(),
            'origin_name' => $file->getOriginalName(),
        ];
    }
}
