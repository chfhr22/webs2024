<?php
    include "../connect/connect.php";
    include "../connect/session.php";

    // echo "<pre>";
    // var_dump($_SESSION);
    // echo "</pre>";
    // 총 페이지 개수
    $sql = "SELECT count(boardID) FROM board";
    $result = $connect -> query($sql);
    $boardTotalCount = $result -> fetch_array(MYSQLI_ASSOC);
    $boardTotalCount = $boardTotalCount['count(boardID)'];
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP 블로그 만들기</title>

    <!-- CSS -->
    <?php include "../include/head.php"?>
</head>
<body class="gray">
    <?php include "../include/skip.php"?>
    <!-- //skip -->

    <?php include "../include/header.php"?>
    <!-- //header -->

    <main id="main" role="main">
    <div class="intro__inner bmStyle container">
            <div class="intro__img small">
                <img srcset="../asset/img/intro03@1x.jpg 1x, ../asset/img/intro03@2x.jpg 2x, ../asset/img/intro03@3x.jpg 3x" alt="인트로 이미지">
            </div>
            <div class="intro__text">
                <h2>게시판</h2>
                <p>개발자를 위한 게시판 입니다.</p>
            </div>
        </div>
        <section class="board__inner container">

        <div class="board__search">
            <div class="left">
                * 총 <em><?=$boardTotalCount?></em>건의 게시물이 등록되어 있습니다.
            </div>
            <div class="right">
                <form action="boardSearch.php" name="boardSearch" method="get">
                    <fieldset>
                        <legend class="blind">게시판 검색 영역</legend>
                        <input type="search" name="searchKeyword" id="searchKeyword" placeholder="검색어를 입력하세요!" required>
                        <select name="searchOption" id="searchOption">
                            <option value="title">제목</option>
                            <option value="content">내용</option>
                            <option value="name">등록자</option>
                        </select>
                        <button type="submit" class="btn__style3 white">검색</button>
                        <a href="boardWrite.php" class="btn__style3">글쓰기</a>
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="board__table">
                <table>
                    <colgroup>
                        <col style="width: 5%;">
                        <col>
                        <col style="width: 10%;">
                        <col style="width: 15%;">
                        <col style="width: 7%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>번호</th>
                            <th>제목</th>
                            <th>등록자</th>
                            <th>등록일</th>
                            <th>조회수</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
    if(isset($_GET['page'])){
        $page = (int) $_GET['page'];
    } else {
        $page = 1;
    }
    $viewNum = 10;
    $viewLmit = ($viewNum * $page) - $viewNum;
    // 1~10 LIMIT 0, 10     --> page1   ($viewNum * 1) - $viewNum
    // 11~20 LIMIT 10, 10   --> page2   ($viewNum * 2) - $viewNum

    $sql = "SELECT b.boardID, b.boardTitle, m.youName, b.regTime, b.boardView FROM board b JOIN members m ON(b.memberID = m.memberID) ORDER BY boardID DESC LIMIT {$viewLmit}, {$viewNum}";
    $result = $connect -> query($sql);

    if($result){
        $count = $result -> num_rows;

        if($count > 0){
            for($i=0; $i<$count; $i++){
                $info = $result -> fetch_array(MYSQLI_ASSOC);

                echo "<tr>";
                echo "<td>".$info['boardID']."</td>";
                echo "<td><a href='boardView.php?boardID={$info['boardID']}'>".$info['boardTitle']."</a></td>";
                echo "<td>".$info['youName']."</td>";
                echo "<td>".date('Y-m-d', $info['regTime'])."</td>";
                echo "<td>".$info['boardView']."</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colsapn='5'>게시글이 없습니다.</td></tr>";
        }
    } else {
        echo "관리자에게 문의해주세요";
    }
?>
                    </tbody>
                </table>
            </div>
            <!-- //board__table -->

            <div class="board__pages">
                <ul>
<?php
    // 총 페이지 개수
    $boardTotalCount = ceil($boardTotalCount/$viewNum);

    $pageView = 5;
    $startPage = $page - $pageView;
    $endPage = $page + $pageView;

    // 처음페이지와 마지막페이지 초기화
    if($startPage < 1) $startPage = 1;
    if($endPage >= $boardTotalCount) $endPage = $boardTotalCount;

    // 페이지
    $prevPage = $page - 1;
    if ($prevPage < 1) $prevPage = 1;

    echo "<li class='first'><a href='board.php?page=1'>처음으로</a></li>";
    echo "<li class='prev'><a href='board.php?page={$prevPage}'>이전</a></li>";

    for($i=$startPage; $i<=$endPage; $i++){
        if ($i == $page) {      
            echo "<li class='active'><a href='board.php?page={$i}'>${i}</a></li>";  // 현재 페이지인 경우에 .active 클래스 추가
        } else {
            echo "<li><a href='board.php?page={$i}'>${i}</a></li>";
        }
    }

    $nextPage = $page + 1;
    if ($nextPage > $boardTotalCount) $nextPage = $page;

    echo "<li class='next'><a href='board.php?page={$nextPage}'>다음</a></li>";
    echo "<li class='last'><a href='board.php?page={$boardTotalCount}'>마지막으로</a></li>";
?>
                </ul>
            </div>
            <!-- //board__pages -->
        </section>
        <!-- //board__inner -->
    </main>
    <!-- //main -->

    <?php include "../include/footer.php"?>
    <!-- //footer -->
</body>
</html>