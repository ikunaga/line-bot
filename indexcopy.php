<?php

//[1]署名の検証
   //composerでインストールしたライブラリを読み込み
   require_once __DIR__ . '/vendor/autoload.php';

   //アクセストークンを使いCurlHTTPClientをインスタンス化
   $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

   //CurlHTTPClientとシークレットを使いLINEbotをインスタンス化
   $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

   //LINEMessagingAPIがリクエストに付与した署名を取得
   $signature = $_SERVER['HTTP_' . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

   //署名が正当かチェック。正当であればリクエストをパースし配列へ
   try {
      $events = $bot->parseEventRequest(file_get_contents('php://input'),$signature);

   } catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
      error_log('parseEventRequest failed. InvalidSignatureException => '.var_export($e, true));
   } catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
      error_log('parseEventRequest failed. UnknownEventTypeException => '.var_export($e, true));
   } catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
      error_log('parseEventRequest failed. UnknownMessageTypeException => '.var_export($e, true));
   } catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
      error_log('parseEventRequest failed. InvalidEventRequestException => '.var_export($e, true));
   }

//[2]メッセージタイプのフィルタ

// 配列に格納された各イベントをループで処理
foreach ($events as $event) {
   // テキストを返信
   // $bot->replyText($event->getReplyToken(), $event->getText());
 
      //返信メッセージ
      $return_message_text = "「" . $message_text . "」なの？";

      //返信実行
      $this->sending_messages($accessToken, $replyToken, $message_type, $return_message_text);


   //テキストを返信し次のイベントの処理へ
   // replyTextMessage($bot, $event->getReplyToken(), 'TextMessage');

   //画像を返信
   // replyTextMessage($bot, $event->getReplyToken(), 'https://' . 
   // $_SERVER['HTTP_HOST'] . 
   // '/imgs/tsol_logo.jpg',
   // 'https://' . $_SERVER['HTTP_HOST'] . 
   // '/imgs/tower.jpg');
}



//テキストを返信。引数はLINEbot、返信先、テキスト
function replyTextMessage($bot, $replyToken, $text) {
   //返信を行いレスポンスを取得
   //TextMessageBuilderの引数はテキスト
   $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));

   //レスポンスが異常な場合
   if (!$response->isSucceeded()) {
      //エラー内容を出力
      error_log('Failed!'. $response->getHTTPStatus . '　' . $response->getRawBody());

   }
}

//画像を返信。引数はLINEBot、返信先、画像URL、サムネイルURL
function replyImageMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl) {
   //ImageMessageBuilderの引数は画像URL、サムネイルURL
   $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl, $previewImageUrl));
   if (!$response->isSucceeded()) {
      error_log('Failed!'. $response->getHTTPStatus . '　' . $response->getRawBody());
   }
}
?>