# Discord RP tool
Ever wanted to RP on discord, but been distracted by your own avatar not matching your character? Felt uncomfortable with changing your avatar to your character? Found it annoying to distinguish IC posts and OOC comments on your account?
This was my solution to those kinds of situations: make a separate bot account for your character and post from there.

## Requirements
On the server-side, you just need PHP, and you need to be able to run [composer](https://getcomposer.org/). That's probably it. I've only run this in PHP 7.

## Installation
I'm going to give a step-by-step installation instruction, mostly for people who usually don't code but want to give a try at prettifying their RP experience. Experienced people can skim over this.

### Add Libraries from Composer
- [RestCord](https://www.restcord.com/) ([Github](https://github.com/restcord/restcord))
- [Parsedown](https://github.com/erusev/parsedown)

The path to vendor is written in index.php, as being in the same directory as this script. If you install the libraries elsewhere, please rewrite the path accordingly.

### Making the bot
Make your bot application on the [discord developer portal](https://discordapp.com/developers/applications/). From the Settings, pick Bot and create the bot account. You'll be seeing the username and the avatar a lot, so make sure to set those to match your character. I'd also turn off the Public Bot option; you don't need your RP character to be running around elsewhere, do you? (This may vary depending on whether you have admin permissions in the server you want to RP on.)

### Copy the bot data
Copy the client id from the General Information tab (you'll need it for authorizing your bot later), and copy the token from the Bot tab (you'll need it for actually running the bot script). You should keep the token safe and never let anyone access it.

### Edit discord.ini
Put your token into the file, replacing the placeholder I put there.
You can also set the [date-time format](http://php.net/manual/en/function.date.php) here, as well as your timezone for displaying the time.

### Do the gateway connection
You need to connect your bot to the gateway once.
Download the [gateway.html](https://github.com/restcord/restcord/blob/master/extra/gateway.html) from the restcord repository, paste your token in the field, and run it once. Your bot will be good for posting once you do this.

### Invite the bot to your server
[Choose the permissions](https://discordapi.com/permissions.html#68608) you want to give your bot. The linked pages as the minimum requirements. Tick more boxes to add anything (like sending links) if you think you'll need them.

Once you're sure you've gave the right amount of permission to your bot, put in the client id from your bot we copied earlier, and click on the link to invite the bot.
Note that you need admin permission to add bots to servers.
If you change your mind about bot permissions, you can always make a new link here to re-add your bot.

### Done
And that should be it.

## Known problems
If there are channels on the server that your bot doesn't have read access to, clicking on those channels gives you a 403 error. I still haven't figured out how to deal with that other than just ignoring those channels.
Also this doesn't work with DMs.

# ディスコードTRPGツール
ディスコードでTRPGをしたいと思った時、サーバーごとのニックネームは設定できてもプロフィール画像は全体で共通なのが落ち着かないとか、あるいはPC発言とPL発言の区別をつけるのが面倒くさいとか、そういう経験を踏まえてキャラ用にbotアカウントを運用するために個人的に書いたツールです。

## 要件
PHPさえ動けばよいので、共用レンタルサーバーで動かせます。[composer](https://getcomposer.org/)を使える必要があります。PHP7でしか動作を確認してません。

## 導入手順
念のためになるべく詳しく書いておきます。

### Composerでライブラリを入れる
- [RestCord](https://www.restcord.com/) ([Github](https://github.com/restcord/restcord))
- [Parsedown](https://github.com/erusev/parsedown)

Composerの使い方は各ライブラリのページを見てください。

このリポジトリの中身と同じフォルダにvendorを入れる想定でindex.phpを書いているので、もし違う場所に入れた場合は適切にパスを書き換えて使ってください。

### Bot作成
[discord developer portal](https://discordapp.com/developers/applications/)でアプリを作ります。アプリを作れたら横のメニューからBotを選び、サーバーに参加させることになるbotアカウントを作成します。usernameとアバターが表示されるのでキャラクターに合わせて設定しましょう。

誰でも自由にサーバーに追加できる必要はないだろうと思うので、botを使いたいサーバーの管理人ではなくて管理人にbotを追加して貰う必要があるのでなければPublic Botはオフにしてよいと思います。

### Botの鍵
General Informationタブからclient idをコピーしておきます。これは後でbotをサーバーに招待するときに使います。
BotタブからTokenをコピーします。これはbotスクリプトに使います。Tokenを人に知られるとbotを乗っ取られるので厳重に保管しましょう。

### discord.iniの編集
Tokenを置き換えてください。
任意で表示される[日付形式](http://php.net/manual/ja/function.date.php)やタイムゾーンも設定できます。

### Gatewayに一度接続
Botからの投稿を有効化するために一度gatewayに接続する必要があります。Restcordリポジトリの[gateway.html](https://github.com/restcord/restcord/blob/master/extra/gateway.html)をDLし、Tokenを入力して一度接続すれば十分です。

### サーバーに招待
Botに付与したい[権限を選択して](https://discordapi.com/permissions.html#68608)、先ほどコピーしておいたclient idを貼り付けてリンクを生成します。選択済みの権限で読み書きは十分ですが、たとえばリンクを含めたい場合などは追加で必要そうな項目も選択してください。

十分な権限を選択できたらリンクをクリックし、サーバーに追加できます。この時、サーバーへのbotの追加ができるのはサーバーで管理権限を有したユーザーだけである点に留意してください。botの権限はまたこのページでリンクを生成しなおせば変更できます。

### 完了
……と、これで動作するはずです。

## 既知の問題
サーバー上にbotのアクセスが許可されていないチャンネルがある場合、該当チャンネルを選択しようとすると403エラーが出ます。一覧から除外するよい方法が思いつかないので一度403を食らったチャンネルは以後クリックしないようにしてください。
また、ユーザーとのDMをする方法はありません。
