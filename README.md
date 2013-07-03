wp-recent-views
=============

ユーザーの最近見たページを表示したりするWordPressのプラグインを作ってみたり。
テンプレートタグ、list_recent_viewsは、ajaxにも対応したよ。
www.warna.info で絶賛テスト中

www.warna.info での記述
<?php if ( function_exists( 'list_recent_views' ) ) list_recent_views( 'title_li=&show_option_none=&mode=ajax&limit=5' ); ?>