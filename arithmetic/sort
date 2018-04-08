<?php
$arr=array(2,5,4,12,82,14,98,36);
print_r($arr);
function bubbleSort($arr)
{
	$len=count($arr);
	//外层循环控制需要进行多少次冒泡，每次冒泡将最大数放在后面，总共需要$len-1次冒泡
	for($i=0;$i<$len-1;$i++)
	{
		//内层循环次数，第一次求得最大数据$len-1次，第二次求得第二大数$len-2,...最后一次1次
		for($j=0;$j<$len-$i-1;$j++)
		{
			if($arr[$j]>$arr[$j+1])
			{
				list($arr[$j],$arr[$j+1])=array($arr[$j+1],$arr[$j]);
			}
		}
	}
	
	return $arr;
}

print_r(bubbleSort($arr));

function quickSort($arr)
{
	$len=count($arr);
	if($len<=1) return $arr;
	
	$leftArr=array();
	$rightArr=array();
	
	$middle=$arr[0];

	for($i=1;$i<$len;$i++)
	{
		if($arr[$i]>$middle)
		{
			$rightArr[]=$arr[$i];
		}
		else
		{
			$leftArr[]=$arr[$i];
		}
	}

	$leftArr=quickSort($leftArr);
	$rightArr=quickSort($rightArr);

	$arr=array_merge($leftArr,array($middle),$rightArr);
	//print_r($arr);
	return $arr;
}

print_r(quickSort($arr));

function selectSort($arr)
{
	$len=count($arr);
	//需要有几个数比较最小值
	for($i=0;$i<$len-1;++$i)  
	{
		$p=$i;	//假定当前最小
		
		//第一次求得最小值索引需要$len-1次,第二次需要$len-2次...最后一次需要1次
		for($j=$i+1;$j<$len;++$j)
		{
			if($arr[$p]>$arr[$j])
			{
				$p=$j;		//求得这里面最小数的索引
			}
		}

		if($p !=$i)
		{
			list($arr[$i],$arr[$p])=array($arr[$p],$arr[$i]);
		}
	}
	
	return $arr;
}

print_r(selectSort($arr));

function insertSort($arr) {
    //区分 哪部分是已经排序好的
    //哪部分是没有排序的
    //找到其中一个需要排序的元素
    //这个元素 就是从第二个元素开始，到最后一个元素都是这个需要排序的元素
    //利用循环就可以标志出来
    //i循环控制 每次需要插入的元素，一旦需要插入的元素控制好了，
    //间接已经将数组分成了2部分，下标小于当前的（左边的），是排序好的序列
    $len=count($arr);
	for($i=1; $i<$len; $i++) {
        //获得当前需要比较的元素值。
        $tmp = $arr[$i];
		
        //内层循环控制 比较 并 插入
        for($j=$i-1;$j>=0;$j--) {
		//$arr[$i];//需要插入的元素; $arr[$j];//需要比较的元素
            if($tmp < $arr[$j]) {
				echo $i;
                //发现插入的元素要小，交换位置
                //将后边的元素与前面的元素互换
                $arr[$j+1] = $arr[$j];
                //将前面的数设置为 当前需要交换的数
                $arr[$j] = $tmp;
				var_dump('--temp:'.$tmp.'---j:'.$j.'aj:'.$arr[$j].'<br/>');
            } else {
                //如果碰到不需要移动的元素
           //由于是已经排序好是数组，则前面的就不需要再次比较了。
                break;
            }
        }
    }
    //将这个元素 插入到已经排序好的序列内。
    //返回
    return $arr;
}


print_r(insertSort($arr));
