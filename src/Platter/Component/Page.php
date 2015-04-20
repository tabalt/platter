<?php

namespace Platter\Component;

class Page
{

    /**
     * 总记录条数
     * @author tabalt
     * @var string
     */
    private $totalNum;

    /**
     * 每页显示的条数
     * @author tabalt
     * @var string
     */
    private $pageSize;

    /**
     * 分页起始条数
     * @author tabalt
     * @var string
     */
    private $limitStart;

    /**
     * 显示 配置
     * @author tabalt
     * @var string
     */
    private $config = array(
        'header' => '条记录',  // 个会员、篇文章、rows、 total
        'prev' => '上一页',  // <<、prev
        'next' => '下一页',  // >>、next
        'first' => '首页',  // 第一页、first
        'last' => '末页',  // 最后一页、last
        'page' => '页',  // page
        'split' => '/' // of,
        );

    /**
     * 当前页
     * @author tabalt
     * @var string
     */
    private $nowPage;

    /**
     * 总页数
     * @author tabalt
     * @var int
     */
    private $totalPage;

    /**
     * baseUrl
     * @author tabalt
     * @var string
     */
    private $baseUrl;

    /**
     * 1,2,3,4 （5）6,7,8,9 页码列表 除当前页的个数
     * @author tabalt
     * @var string
     */
    private $listNum;

    /**
     * 可能的url 情况
     * 1、'/xxx/admin.php'
     * 2、'/xxx/admin.php?' 要删除后面的?
     * 3、'/xxx/admin.php?m=Channel&a=mContent&cid=3&method=manage'
     * 4、'/xxx/admin.php?m=Channel&a=mContent&cid=3&method=manage&page=3' 要删除page=3
     */
    private function setbaseUrl()
    {
        // $_SERVER["REQUEST_URI"] = '/xxx/admin.php' ;
        $url = rtrim($_SERVER["REQUEST_URI"], '?');
        // echo $url.'<br />';
        $parse = parse_url($url);
        // var_dump( $parse);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $output);
            // var_dump($output);
            unset($output['page']);
            $url = $parse['path'] . '?' . http_build_query($output) . '&';
        } else {
            $url .= '?';
        }
        $this->baseUrl = $url;
    }

    /**
     * 生成链接
     * @author tabalt
     * @param int $page
     * @param string $title
     * @param string $class
     * @param string $id
     */
    private function getPageLink($page, $title, $class = false, $id = false)
    {
        $tpl = '<li ###CLASS###><a href="###URL###" ###ID### title="###TITLE###" >###TITLE###</a></li>';
        $search = array(
            '###URL###', 
            '###TITLE###', 
            '###CLASS###', 
            '###ID###'
        );
        if ($class) {
            $class = 'class="' . $class . '"';
        }
        if ($id) {
            $id = 'id="' . $id . '"';
        }
        $url = $this->baseUrl . $this->pageQueryString($page);
        $url = rtrim($url, "?&");
        $replace = array(
            $url, 
            $title, 
            $class, 
            $id
        );
        
        $link = str_replace($search, $replace, $tpl);
        return $link;
    }

    private function pageQueryString($page)
    {
        $url = '';
        // $page 为1 则代表 是首页
        
        if ($page != 1) {
            $url = 'page=' . $page;
        }
        return $url;
    }

    /**
     * 首页页码
     * @author tabalt
     */
    private function getFirstPage()
    {
        return false;
    }

    /**
     * 上一页页码
     * @author tabalt
     */
    private function getPrevPage()
    {
        $prevPage = ($this->nowPage > 1) ? ($this->nowPage - 1) : 1;
        return $prevPage;
    }

    /**
     * 下一页页码
     * @author tabalt
     */
    private function getNextPage()
    {
        $nextPage = ($this->nowPage < $this->totalPage) ? ($this->nowPage + 1) : $this->totalPage;
        return $nextPage;
    }

    /**
     * 末页页码
     * @author tabalt
     */
    private function getLastPage()
    {
        return $this->totalPage;
    }

    /**
     * 构造方法
     * @author tabalt
     */
    public function __construct($totalNum, $pageSize = 10, $listNum = 8, $config = "")
    {
        $this->totalNum = $totalNum;
        $this->pageSize = $pageSize;
        $this->totalPage = ceil($totalNum / $pageSize);
        $this->setNowPage(1);
        $this->setbaseUrl();
        $this->listNum = $listNum;
        if (! empty($config)) {
            $this->config = $config;
        }
    }

    /**
     * 获取首页链接
     * @author tabalt
     * @param string $class
     * @param string $id
     */
    public function getFirstLink($class = false, $id = false)
    {
        return $this->getPageLink($this->getFirstPage(), $this->config['first'], $class, $id);
    }

    /**
     * 获取上一页链接
     * @author tabalt
     * @param string $class
     * @param string $id
     */
    public function getPrevLink($class = false, $id = false)
    {
        return $this->getPageLink($this->getPrevPage(), $this->config['prev'], $class, $id);
    }

    /**
     * 获取下一页链接
     * @author tabalt
     * @param string $class
     * @param string $id
     */
    public function getNextLink($class = false, $id = false)
    {
        return $this->getPageLink($this->getNextPage(), $this->config['next'], $class, $id);
    }

    /**
     * 获取末页链接
     * @author tabalt
     * @param string $class
     * @param string $id
     */
    public function getLastLink($class = false, $id = false)
    {
        return $this->getPageLink($this->getLastPage(), $this->config['last'], $class, $id);
    }

    /**
     * 获取当前页以前的链接列表
     * @author tabalt
     * @param string $class
     * @param string $id
     */
    public function getPrevLinkList($class = false)
    {
        $str = "";
        for ($i = ($this->listNum / 2); $i >= 1; $i --) {
            $pre = $this->nowPage - $i;
            if ($pre >= 1) {
                $str .= $this->getPageLink($pre, $pre, $class);
            }
        }
        return $str;
    }

    /**
     * 获取当前页以后的链接列表
     * @author tabalt
     * @param string $class
     * @param string $id
     */
    public function getNextLinkList($class = false)
    {
        $str = "";
        for ($i = 1; $i <= ($this->listNum / 2); $i ++) {
            $next = $this->nowPage + $i;
            if ($next <= $this->totalPage) {
                $str .= $this->getPageLink($next, $next, $class);
            }
        }
        return $str;
    }

    /**
     * 获取链接列表
     * @author tabalt
     * @param string $class
     * @param string $id
     */
    public function getLinkList($class = false, $activeClass = false)
    {
        $str = "";
        $str .= $this->getPrevLinkList($class);
        $str .= $this->getPageLink($this->nowPage, $this->nowPage, $activeClass);
        $str .= $this->getNextLinkList($class);
        return $str;
    }

    /**
     * 获取分页头
     * @author tabalt
     */
    public function getPageHeader()
    {
        $header = '';
        if ($this->totalPage) {
            // 1/2页, 1 of 2 page
            $header .= $this->nowPage . $this->config['split'] . $this->totalPage . $this->config['page'] . " ";
            // 2篇文章, 2 articles
            $header .= $this->totalNum . $this->config['header'];
        }
        return "<li class=\"disabled\"><span class=\"page_header\" >" . $header . "</span></li>";
    }

    /**
     * 设置当前页
     * @author tabalt
     * @param int $nowPage
     */
    public function setNowPage($nowPage)
    {
        $nowPage = intval($nowPage);
        // 小于1 则等于1
        if ($nowPage < 1) {
            $nowPage = 1;
        }
        
        // 超过 总条数 则等于 总条数
        if ($nowPage > $this->totalPage) {
            $nowPage = $this->totalPage;
        }
        $this->nowPage = $nowPage;
        
        // (当前页码 -1)*每页条数
        // 0,10 11,20
        $this->limitStart = ($nowPage - 1) * $this->pageSize;
        if ($this->limitStart < 0) {
            $this->limitStart = 0;
        }
    }

    /**
     * 获取分页的起始条数
     * @author tabalt
     */
    public function getLimitStart()
    {
        return $this->limitStart;
    }

    /**
     * 获取每页条数
     * @author tabalt
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * 获取总页数
     * @author tabalt
     */
    public function getTotalPage()
    {
        return $this->totalPage;
    }

    /**
     * 获取page limit条件参数
     * @author tabalt
     */
    public function getPageLimit()
    {
        $limit = "{$this->limitStart},{$this->pageSize}";
        return $limit;
    }

    /**
     * 获取分页html代码
     * "1/1页 2条记录（每页10条，当前从1到10条） 首页 上一页1234下一页 尾页 ";
     * @author tabalt
     * @param $activeClass
     * @param $linkClass
     */
    public function getPageHtml($activeClass, $linkClass)
    {
        if ($this->totalNum == 0) {
            return "";
        }
        
        $pageInfos = "<ul class=\"pagination\" >";
        if ($this->totalPage) {
            $pageInfos .= $this->getPageHeader(); // 分页头
            $pageInfos .= $this->getFirstLink($linkClass); // 首页、first
            $pageInfos .= $this->getPrevLink($linkClass); // 上一页、prev
            $pageInfos .= $this->getLinkList($linkClass, $activeClass);
            $pageInfos .= $this->getNextLink($linkClass); // 下一页、next
            $pageInfos .= $this->getLastLink($linkClass); // 尾页、last
        }
        $pageInfos .= "</ul>\n";
        return $pageInfos;
    }
}
?>