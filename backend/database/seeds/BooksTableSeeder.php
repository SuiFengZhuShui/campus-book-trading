<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BooksTableSeeder extends Seeder
{
    private $courseIds = [];
    private $sellerIds = [];
    private $categoryIds = [];

    public function run()
    {
        $this->seedCollegesAndCourses();
        $this->seedSellers();
        $this->loadCategories();
        $this->seedBooks();
    }

    private function seedCollegesAndCourses()
    {
        $colleges = [
            '电子信息工程学院' => [
                '软件工程' => ['数据结构与算法', '操作系统', '计算机网络', '数据库原理', 'C语言程序设计', 'Java程序设计', 'Python程序设计'],
            ],
            '机电工程学院' => [
                '机械工程' => ['机械制图', '机械设计', '电工学', '工程力学', '液压与气压传动'],
            ],
            '财经与物流管理学院' => [
                '会计学' => ['基础会计', '财务管理学', '中级财务会计', '成本会计学', '审计学'],
                '物流管理' => ['物流管理概论', '供应链管理', '管理会计学'],
            ],
            '环境与食品学院' => [
                '环境工程' => ['环境科学概论', '环境监测', '水污染控制工程', '环境工程原理'],
                '食品科学' => ['食品化学', '食品微生物学', '食品安全学'],
            ],
            '汽车工程学院' => [
                '车辆工程' => ['汽车构造', '汽车理论', '发动机原理', '汽车电子控制技术'],
            ],
            '贸易与旅游学院' => [
                '国际经济与贸易' => ['国际贸易实务', '市场营销学', '商务谈判', '消费者行为学'],
                '旅游管理' => ['旅游学概论', '导游业务', '酒店管理概论'],
            ],
            '艺术学院' => [
                '视觉传达设计' => ['设计概论', '色彩构成', '平面设计基础', '构成基础'],
            ],
        ];

        $collegeSort = 1;
        foreach ($colleges as $collegeName => $majors) {
            $collegeId = DB::table('colleges')->insertGetId([
                'name' => $collegeName,
                'sort' => $collegeSort++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($majors as $majorName => $courses) {
                $majorId = DB::table('majors')->insertGetId([
                    'college_id' => $collegeId,
                    'name' => $majorName,
                    'sort' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($courses as $c) {
                    $this->courseIds[$collegeName][] = DB::table('courses')->insertGetId([
                        'major_id' => $majorId,
                        'name' => $c,
                        'sort' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function seedSellers()
    {
        $students = [
            ['name' => '张三', 'phone' => '13800000001'],
            ['name' => '李四', 'phone' => '13800000002'],
            ['name' => '王五', 'phone' => '13800000003'],
            ['name' => '赵六', 'phone' => '13800000004'],
            ['name' => '陈七', 'phone' => '13800000005'],
            ['name' => '刘明', 'phone' => '13800000006'],
            ['name' => '周杰', 'phone' => '13800000007'],
            ['name' => '吴芳', 'phone' => '13800000008'],
            ['name' => '郑浩', 'phone' => '13800000009'],
            ['name' => '钱丽', 'phone' => '13800000010'],
            ['name' => '孙鹏', 'phone' => '13800000011'],
            ['name' => '杨雪', 'phone' => '13800000012'],
            ['name' => '黄涛', 'phone' => '13800000013'],
            ['name' => '许琳', 'phone' => '13800000014'],
            ['name' => '林峰', 'phone' => '13800000015'],
        ];

        foreach ($students as $s) {
            $this->sellerIds[] = DB::table('users')->insertGetId([
                'name' => $s['name'],
                'phone' => $s['phone'],
                'password' => Hash::make('REDACTED-PASSWORD'),
                'role' => 'student',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function loadCategories()
    {
        $this->categoryIds = DB::table('categories')->pluck('id', 'name')->toArray();
    }

    private function seedBooks()
    {
        $cat = $this->categoryIds;

        $books = [
            // ====== 电子信息工程学院 (8本) ======
            [
                'title' => '数据结构（C语言版）',
                'author' => '严蔚敏',
                'publisher' => '清华大学出版社',
                'isbn' => '9787302023685',
                'category' => '电子信息工程学院',
                'condition' => 'excellent',
                'original_price' => 39.00,
                'price' => 18.00,
                'cost_price' => 10.00,
                'description' => '几乎全新，只有第一章划过几笔',
            ],
            [
                'title' => '计算机网络（第7版）',
                'author' => '谢希仁',
                'publisher' => '电子工业出版社',
                'isbn' => '9787121302954',
                'category' => '电子信息工程学院',
                'condition' => 'like_new',
                'original_price' => 59.00,
                'price' => 30.00,
                'cost_price' => 18.00,
                'description' => '几乎没用过，跟新书一样',
            ],
            [
                'title' => '模拟电子技术基础（第5版）',
                'author' => '童诗白',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040422345',
                'category' => '电子信息工程学院',
                'condition' => 'good',
                'original_price' => 49.00,
                'price' => 22.00,
                'cost_price' => 12.00,
                'description' => '正常使用痕迹，实验报告齐全',
            ],
            [
                'title' => '数字电子技术基础（第6版）',
                'author' => '阎石',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040456723',
                'category' => '电子信息工程学院',
                'condition' => 'excellent',
                'original_price' => 45.00,
                'price' => 20.00,
                'cost_price' => 11.00,
                'description' => '只有前几章有笔记，后面全新',
            ],
            [
                'title' => '信号与系统（第2版）',
                'author' => '奥本海姆',
                'publisher' => '电子工业出版社',
                'isbn' => '9787121143892',
                'category' => '电子信息工程学院',
                'condition' => 'good',
                'original_price' => 69.00,
                'price' => 30.00,
                'cost_price' => 17.00,
                'description' => '经典教材，有少量标注',
            ],
            [
                'title' => '通信原理（第7版）',
                'author' => '樊昌信',
                'publisher' => '国防工业出版社',
                'isbn' => '9787118085432',
                'category' => '电子信息工程学院',
                'condition' => 'fair',
                'original_price' => 49.00,
                'price' => 18.00,
                'cost_price' => 9.00,
                'description' => '笔记较多但都是重点，配课后习题答案',
            ],
            [
                'title' => 'C程序设计（第5版）',
                'author' => '谭浩强',
                'publisher' => '清华大学出版社',
                'isbn' => '9787302481443',
                'category' => '电子信息工程学院',
                'condition' => 'like_new',
                'original_price' => 39.00,
                'price' => 20.00,
                'cost_price' => 11.00,
                'description' => '买来没怎么翻，全新一样',
            ],
            [
                'title' => '微机原理与接口技术（第2版）',
                'author' => '周荷琴',
                'publisher' => '中国科学技术大学出版社',
                'isbn' => '9787312032128',
                'category' => '电子信息工程学院',
                'condition' => 'excellent',
                'original_price' => 42.00,
                'price' => 19.00,
                'cost_price' => 10.00,
                'description' => '保管良好，附实验指导书',
            ],

            // ====== 机电工程学院 (8本) ======
            [
                'title' => '机械制图（第7版）',
                'author' => '何铭新',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040423456',
                'category' => '机电工程学院',
                'condition' => 'excellent',
                'original_price' => 42.00,
                'price' => 19.00,
                'cost_price' => 11.00,
                'description' => '图纸没画过，跟新的差不多',
            ],
            [
                'title' => '机械设计（第10版）',
                'author' => '濮良贵',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040543210',
                'category' => '机电工程学院',
                'condition' => 'good',
                'original_price' => 52.00,
                'price' => 25.00,
                'cost_price' => 14.00,
                'description' => '有少量标注，不影响使用',
            ],
            [
                'title' => '电工学（第7版）上册',
                'author' => '秦曾煌',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040432106',
                'category' => '机电工程学院',
                'condition' => 'fair',
                'original_price' => 45.00,
                'price' => 15.00,
                'cost_price' => 8.00,
                'description' => '笔记较多，但重点都有',
            ],
            [
                'title' => '工程力学（第5版）',
                'author' => '张秉荣',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111543212',
                'category' => '机电工程学院',
                'condition' => 'excellent',
                'original_price' => 48.00,
                'price' => 22.00,
                'cost_price' => 12.00,
                'description' => '几乎全新，只用了一学期',
            ],
            [
                'title' => '机械制造基础（第2版）',
                'author' => '王先逵',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111456732',
                'category' => '机电工程学院',
                'condition' => 'good',
                'original_price' => 55.00,
                'price' => 26.00,
                'cost_price' => 14.00,
                'description' => '正常使用痕迹，内容完整',
            ],
            [
                'title' => '数控技术（第3版）',
                'author' => '王永章',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040343214',
                'category' => '机电工程学院',
                'condition' => 'like_new',
                'original_price' => 38.00,
                'price' => 20.00,
                'cost_price' => 11.00,
                'description' => '选修课买的，没怎么用',
            ],
            [
                'title' => '液压与气压传动（第4版）',
                'author' => '许福玲',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111567843',
                'category' => '机电工程学院',
                'condition' => 'good',
                'original_price' => 46.00,
                'price' => 21.00,
                'cost_price' => 11.00,
                'description' => '内页干净，书脊完好',
            ],
            [
                'title' => '公差配合与测量技术（第6版）',
                'author' => '廖念钊',
                'publisher' => '中国质检出版社',
                'isbn' => '9787502654321',
                'category' => '机电工程学院',
                'condition' => 'excellent',
                'original_price' => 35.00,
                'price' => 16.00,
                'cost_price' => 8.00,
                'description' => '只用过前几章，后面都是新的',
            ],

            // ====== 财经与物流管理学院 (8本) ======
            [
                'title' => '基础会计（第6版）',
                'author' => '陈国辉',
                'publisher' => '东北财经大学出版社',
                'isbn' => '9787565432101',
                'category' => '财经与物流管理学院',
                'condition' => 'excellent',
                'original_price' => 38.00,
                'price' => 17.00,
                'cost_price' => 9.00,
                'description' => '只用了一学期，保管完好',
            ],
            [
                'title' => '财务管理学（第8版）',
                'author' => '荆新',
                'publisher' => '中国人民大学出版社',
                'isbn' => '9787300265432',
                'category' => '财经与物流管理学院',
                'condition' => 'like_new',
                'original_price' => 49.00,
                'price' => 26.00,
                'cost_price' => 15.00,
                'description' => '几乎全新，只写了名字',
            ],
            [
                'title' => '物流管理概论（第3版）',
                'author' => '马士华',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111567894',
                'category' => '财经与物流管理学院',
                'condition' => 'good',
                'original_price' => 39.00,
                'price' => 16.00,
                'cost_price' => 9.00,
                'description' => '有部分标注，整体干净',
            ],
            [
                'title' => '中级财务会计（第5版）',
                'author' => '刘永泽',
                'publisher' => '东北财经大学出版社',
                'isbn' => '9787565456786',
                'category' => '财经与物流管理学院',
                'condition' => 'good',
                'original_price' => 52.00,
                'price' => 24.00,
                'cost_price' => 13.00,
                'description' => '重点章节有标注，附录齐全',
            ],
            [
                'title' => '成本会计学（第8版）',
                'author' => '于富生',
                'publisher' => '中国人民大学出版社',
                'isbn' => '9787300256712',
                'category' => '财经与物流管理学院',
                'condition' => 'excellent',
                'original_price' => 45.00,
                'price' => 21.00,
                'cost_price' => 11.00,
                'description' => '几乎没怎么翻过',
            ],
            [
                'title' => '审计学（第4版）',
                'author' => '秦荣生',
                'publisher' => '中国人民大学出版社',
                'isbn' => '9787300276543',
                'category' => '财经与物流管理学院',
                'condition' => 'fair',
                'original_price' => 48.00,
                'price' => 18.00,
                'cost_price' => 9.00,
                'description' => '有较多笔记，但字迹工整',
            ],
            [
                'title' => '供应链管理（第5版）',
                'author' => '马士华',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111543298',
                'category' => '财经与物流管理学院',
                'condition' => 'like_new',
                'original_price' => 49.00,
                'price' => 25.00,
                'cost_price' => 14.00,
                'description' => '买来没看几次，跟新的一样',
            ],
            [
                'title' => '管理会计学',
                'author' => '孙茂竹',
                'publisher' => '中国人民大学出版社',
                'isbn' => '9787300234567',
                'category' => '财经与物流管理学院',
                'condition' => 'excellent',
                'original_price' => 42.00,
                'price' => 19.00,
                'cost_price' => 10.00,
                'description' => '保存完好，光盘未拆',
            ],

            // ====== 环境与食品学院 (7本) ======
            [
                'title' => '环境科学概论（第2版）',
                'author' => '左玉辉',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040234560',
                'category' => '环境与食品学院',
                'condition' => 'excellent',
                'original_price' => 36.00,
                'price' => 16.00,
                'cost_price' => 9.00,
                'description' => '保存很好，无污渍',
            ],
            [
                'title' => '食品化学（第3版）',
                'author' => '阚建全',
                'publisher' => '中国农业大学出版社',
                'isbn' => '9787565543210',
                'category' => '环境与食品学院',
                'condition' => 'good',
                'original_price' => 48.00,
                'price' => 22.00,
                'cost_price' => 12.00,
                'description' => '实验课用过，书脊有标签',
            ],
            [
                'title' => '环境监测（第4版）',
                'author' => '奚旦立',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040321678',
                'category' => '环境与食品学院',
                'condition' => 'excellent',
                'original_price' => 49.00,
                'price' => 23.00,
                'cost_price' => 13.00,
                'description' => '实验报告都在，书很干净',
            ],
            [
                'title' => '食品微生物学（第3版）',
                'author' => '江汉湖',
                'publisher' => '中国农业出版社',
                'isbn' => '9787109143210',
                'category' => '环境与食品学院',
                'condition' => 'good',
                'original_price' => 52.00,
                'price' => 24.00,
                'cost_price' => 13.00,
                'description' => '实验室常用参考书',
            ],
            [
                'title' => '水污染控制工程（第4版）',
                'author' => '高廷耀',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040432143',
                'category' => '环境与食品学院',
                'condition' => 'fair',
                'original_price' => 56.00,
                'price' => 20.00,
                'cost_price' => 10.00,
                'description' => '水量较大，笔记工整有用',
            ],
            [
                'title' => '食品安全学',
                'author' => '钟耀广',
                'publisher' => '化学工业出版社',
                'isbn' => '9787122054320',
                'category' => '环境与食品学院',
                'condition' => 'like_new',
                'original_price' => 39.00,
                'price' => 22.00,
                'cost_price' => 12.00,
                'description' => '几乎全新',
            ],
            [
                'title' => '环境工程原理（第3版）',
                'author' => '胡洪营',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040423451',
                'category' => '环境与食品学院',
                'condition' => 'excellent',
                'original_price' => 55.00,
                'price' => 26.00,
                'cost_price' => 14.00,
                'description' => '只翻过几次，跟新的一样',
            ],

            // ====== 汽车工程学院 (7本) ======
            [
                'title' => '汽车构造（第4版）上册',
                'author' => '陈家瑞',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111234560',
                'category' => '汽车工程学院',
                'condition' => 'good',
                'original_price' => 55.00,
                'price' => 28.00,
                'cost_price' => 16.00,
                'description' => '正常使用，内页干净',
            ],
            [
                'title' => '汽车理论（第6版）',
                'author' => '余志生',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111456789',
                'category' => '汽车工程学院',
                'condition' => 'excellent',
                'original_price' => 45.00,
                'price' => 22.00,
                'cost_price' => 12.00,
                'description' => '几乎全新，只翻过几次',
            ],
            [
                'title' => '汽车电器与电子技术（第3版）',
                'author' => '李春明',
                'publisher' => '北京理工大学出版社',
                'isbn' => '9787564054321',
                'category' => '汽车工程学院',
                'condition' => 'excellent',
                'original_price' => 42.00,
                'price' => 20.00,
                'cost_price' => 11.00,
                'description' => '保管良好，附电路图册',
            ],
            [
                'title' => '发动机原理（第2版）',
                'author' => '陈家瑞',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111545678',
                'category' => '汽车工程学院',
                'condition' => 'good',
                'original_price' => 48.00,
                'price' => 23.00,
                'cost_price' => 12.00,
                'description' => '正常使用，内容完整',
            ],
            [
                'title' => '汽车检测与诊断技术',
                'author' => '曹红兵',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111678943',
                'category' => '汽车工程学院',
                'condition' => 'like_new',
                'original_price' => 45.00,
                'price' => 24.00,
                'cost_price' => 13.00,
                'description' => '只在实训课用过一次',
            ],
            [
                'title' => '汽车电子控制技术',
                'author' => '周云山',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111456790',
                'category' => '汽车工程学院',
                'condition' => 'good',
                'original_price' => 52.00,
                'price' => 25.00,
                'cost_price' => 14.00,
                'description' => '有少量标注，重点章节有贴条',
            ],
            [
                'title' => '新能源汽车技术',
                'author' => '崔胜民',
                'publisher' => '北京大学出版社',
                'isbn' => '9787301267845',
                'category' => '汽车工程学院',
                'condition' => 'excellent',
                'original_price' => 49.00,
                'price' => 27.00,
                'cost_price' => 15.00,
                'description' => '几乎全新，选修课用的',
            ],

            // ====== 贸易与旅游学院 (7本) ======
            [
                'title' => '国际贸易实务（第7版）',
                'author' => '黎孝先',
                'publisher' => '对外经济贸易大学出版社',
                'isbn' => '9787566345678',
                'category' => '贸易与旅游学院',
                'condition' => 'excellent',
                'original_price' => 46.00,
                'price' => 20.00,
                'cost_price' => 11.00,
                'description' => '保存完好，案例新',
            ],
            [
                'title' => '旅游学概论（第3版）',
                'author' => '李天元',
                'publisher' => '南开大学出版社',
                'isbn' => '9787310043210',
                'category' => '贸易与旅游学院',
                'condition' => 'good',
                'original_price' => 34.00,
                'price' => 14.00,
                'cost_price' => 7.00,
                'description' => '有部分划线，笔记工整',
            ],
            [
                'title' => '市场营销学（第6版）',
                'author' => '吴健安',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040456765',
                'category' => '贸易与旅游学院',
                'condition' => 'excellent',
                'original_price' => 42.00,
                'price' => 18.00,
                'cost_price' => 10.00,
                'description' => '只用了一学期，保管完好',
            ],
            [
                'title' => '导游业务（第4版）',
                'author' => '全国导游资格考试统编教材',
                'publisher' => '中国旅游出版社',
                'isbn' => '9787503243212',
                'category' => '贸易与旅游学院',
                'condition' => 'like_new',
                'original_price' => 38.00,
                'price' => 20.00,
                'cost_price' => 11.00,
                'description' => '买来没考成，几乎全新',
            ],
            [
                'title' => '酒店管理概论',
                'author' => '林璧属',
                'publisher' => '东北财经大学出版社',
                'isbn' => '9787565467890',
                'category' => '贸易与旅游学院',
                'condition' => 'good',
                'original_price' => 36.00,
                'price' => 15.00,
                'cost_price' => 8.00,
                'description' => '正常使用痕迹',
            ],
            [
                'title' => '消费者行为学',
                'author' => '符国群',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040345678',
                'category' => '贸易与旅游学院',
                'condition' => 'excellent',
                'original_price' => 45.00,
                'price' => 21.00,
                'cost_price' => 12.00,
                'description' => '保存完好',
            ],
            [
                'title' => '商务谈判（第2版）',
                'author' => '张昊民',
                'publisher' => '高等教育出版社',
                'isbn' => '9787040456790',
                'category' => '贸易与旅游学院',
                'condition' => 'good',
                'original_price' => 35.00,
                'price' => 15.00,
                'cost_price' => 8.00,
                'description' => '有案例标注',
            ],

            // ====== 艺术学院 (7本) ======
            [
                'title' => '设计概论（第2版）',
                'author' => '尹定邦',
                'publisher' => '湖南科学技术出版社',
                'isbn' => '9787535745678',
                'category' => '艺术学院',
                'condition' => 'like_new',
                'original_price' => 48.00,
                'price' => 25.00,
                'cost_price' => 14.00,
                'description' => '买来没怎么用过，几乎全新',
            ],
            [
                'title' => '素描基础教程',
                'author' => '王华祥',
                'publisher' => '中国青年出版社',
                'isbn' => '9787515345678',
                'category' => '艺术学院',
                'condition' => 'good',
                'original_price' => 39.00,
                'price' => 16.00,
                'cost_price' => 8.00,
                'description' => '封面有折角，内页干净',
            ],
            [
                'title' => '色彩构成',
                'author' => '李莉婷',
                'publisher' => '湖北美术出版社',
                'isbn' => '9787539443216',
                'category' => '艺术学院',
                'condition' => 'excellent',
                'original_price' => 48.00,
                'price' => 23.00,
                'cost_price' => 12.00,
                'description' => '只用了几次，色卡齐全',
            ],
            [
                'title' => '平面设计基础',
                'author' => '王受之',
                'publisher' => '中国青年出版社',
                'isbn' => '9787515343223',
                'category' => '艺术学院',
                'condition' => 'excellent',
                'original_price' => 68.00,
                'price' => 32.00,
                'cost_price' => 18.00,
                'description' => '经典教材，案例丰富',
            ],
            [
                'title' => '中外美术史',
                'author' => '中央美术学院美术史系',
                'publisher' => '中国青年出版社',
                'isbn' => '9787515345789',
                'category' => '艺术学院',
                'condition' => 'good',
                'original_price' => 58.00,
                'price' => 26.00,
                'cost_price' => 14.00,
                'description' => '有部分划线，图片页完好',
            ],
            [
                'title' => 'Photoshop CC 设计从入门到精通',
                'author' => '亿瑞设计',
                'publisher' => '清华大学出版社',
                'isbn' => '9787302456321',
                'category' => '艺术学院',
                'condition' => 'like_new',
                'original_price' => 79.00,
                'price' => 38.00,
                'cost_price' => 20.00,
                'description' => '光盘未拆，跟新的一样',
            ],
            [
                'title' => '构成基础',
                'author' => '张海涛',
                'publisher' => '湖南美术出版社',
                'isbn' => '9787535654321',
                'category' => '艺术学院',
                'condition' => 'good',
                'original_price' => 42.00,
                'price' => 18.00,
                'cost_price' => 9.00,
                'description' => '有少量练习痕迹，大部分干净',
            ],
        ];

        $storagePath = storage_path('app/public/books');

        foreach ($books as $i => $data) {
            $sellerIdx = $i % count($this->sellerIds);
            $collegeCourses = $this->courseIds[$data['category']];
            $courseIdx = $i % count($collegeCourses);

            $bookId = DB::table('books')->insertGetId([
                'title' => $data['title'],
                'author' => $data['author'],
                'publisher' => $data['publisher'],
                'isbn' => $data['isbn'],
                'category_id' => $this->categoryIds[$data['category']],
                'course_id' => $collegeCourses[$courseIdx],
                'condition' => $data['condition'],
                'original_price' => $data['original_price'],
                'price' => $data['price'],
                'cost_price' => $data['cost_price'],
                'seller_id' => $this->sellerIds[$sellerIdx],
                'status' => 'active',
                'description' => $data['description'],
                'submitted_at' => now()->subDays(rand(1, 30)),
                'approved_at' => now()->subDays(rand(1, 28)),
                'received_at' => now()->subDays(rand(1, 25)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $dir = "{$storagePath}/{$bookId}";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $colors = [
                ['bg' => [230, 240, 255], 'fg' => [44, 111, 206]],
                ['bg' => [255, 240, 230], 'fg' => [240, 124, 60]],
                ['bg' => [230, 255, 240], 'fg' => [56, 142, 60]],
                ['bg' => [255, 235, 240], 'fg' => [230, 69, 58]],
                ['bg' => [245, 240, 255], 'fg' => [123, 31, 162]],
                ['bg' => [240, 255, 245], 'fg' => [0, 121, 107]],
            ];
            $color = $colors[$i % count($colors)];

            $coverPath = "books/{$bookId}/cover.jpg";
            $this->generatePlaceholder(
                "{$storagePath}/{$bookId}/cover.jpg",
                $data['title'],
                $color['bg'],
                $color['fg']
            );

            DB::table('book_images')->insert([
                'book_id' => $bookId,
                'path' => $coverPath,
                'type' => 'cover',
                'sort' => 0,
                'created_at' => now(),
            ]);

            for ($j = 1; $j <= 2; $j++) {
                $innerPath = "books/{$bookId}/inner_{$j}.jpg";
                $this->generatePlaceholder(
                    "{$storagePath}/{$bookId}/inner_{$j}.jpg",
                    $data['title'] . " - 内页{$j}",
                    [245, 245, 245],
                    [180, 180, 180]
                );
                DB::table('book_images')->insert([
                    'book_id' => $bookId,
                    'path' => $innerPath,
                    'type' => 'inner',
                    'sort' => $j,
                    'created_at' => now(),
                ]);
            }
        }
    }

    private function generatePlaceholder(string $filepath, string $text, array $bg, array $fg)
    {
        $w = 400;
        $h = 560;
        $img = imagecreatetruecolor($w, $h);
        $bgColor = imagecolorallocate($img, $bg[0], $bg[1], $bg[2]);
        $fgColor = imagecolorallocate($img, $fg[0], $fg[1], $fg[2]);

        imagefilledrectangle($img, 0, 0, $w, $h, $bgColor);

        $fontSize = 4;
        $textWidth = imagefontwidth($fontSize) * mb_strlen($text);
        $textHeight = imagefontheight($fontSize);
        $x = max(0, ($w - $textWidth) / 2);
        $y = ($h - $textHeight) / 2;

        imagestring($img, $fontSize, (int)$x, (int)$y, $text, $fgColor);

        imagejpeg($img, $filepath, 85);
        imagedestroy($img);
    }
}
