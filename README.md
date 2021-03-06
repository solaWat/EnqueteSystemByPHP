# EnqueteSystemByPHP
---

## 1．概要
PHP言語で主に書かれているwebアプリケーションです．
学会やゼミなどの発表の場で，発表者を評価するための投票・集計システムとして使用されることを想定しています．

## 2．特徴
* 事前にDATABASEに個人名とIDを登録しておくことで，参加者たちの順番をブラウザ上で，自由に入れ替えて表示させることができます．
* 投票者は，順位によって重みをつけた投票を行うことができます．
* 投票結果は分かりやすいグラフとなって表示され，どの発表者が一番票を集めたのか，一目で確認することができます．

## 3．環境
 LAMP( Linux / Apache / MySQL / PHP)環境 での動作を基本的に想定しています．

## 4．デプロイ方法（ラボ生向け）
* 適当なサーバマシンを用意する．ローカルのラズパイでも，[Amazon Web Service(AWS)](https://docs.aws.amazon.com/ja_jp/AWSEC2/latest/UserGuide/ec2-tutorials.html)を使ってでも良いので，LAMP環境を構築しておく．
### Apacheの場合のデプロイ例を以下に記述する．
* リモートログインしてからデプロイするまでのコマンド
```
cd /var/www/html
sudo rm -r EnqueteSystemByPHP（以前にこれをクローンしたことがある場合）
sudo git clone https://github.com/solaWat/EnqueteSystemByPHP.git
```
* 設定ファイルを本番環境用に
```
sudo chmod 775 EnqueteSystemByPHP（適宜，ディレクトリ権限変更）
cd EnqueteSystemByPHP
sudo chmod 777 var_conf.php 
vim var_conf.php
sudo chmod 644 var_conf.php
```
※ここで，var_conf.php内にて，$dbnameを，
```
$dbname = 'enquetesystembyphp_test';
↓
$dbname = 'enquetesystembyphp';
```
のように書き換える．また，「$pre_dsn・$dsnのコメントアウトの入れ替え（ファイル内を見ればわかる）」を行う．これで開発テスト環境から本番環境で動くっぽくなる．

基本的には以上で動くようになると思う．

### （おまけ）投票結果など（データベース(MySQL)の中身）をファイルにして保存する
参考：[MySQL 5.6 リファレンスマニュアル  /  ...  /  mysqldump — データベースバックアッププログラム](https://dev.mysql.com/doc/refman/5.6/ja/mysqldump.html)

（リモートにて）（MySQLのユーザ名「root」の場合）（sqlファイルの名前はなんでもいい）
```
cd ~
mysqldump -uroot -p enquetesystembyphp > hsymlab_rbrk.sql
```
### （おまけ）ローカルにおけるSQLファイル扱いのススメ
* Macで便利なやつ

　[PHPMyAdminよりも便利なSequel Proの使い方！MacでMySQLの管理が出来る！](https://iritec.jp/web_service/6065/)

* ローカルでの開発環境構築にはMAMP（https://www.mamp.info/en/） が楽チン

　[Sequel Pro と MAMP を繋げる](https://sequelpro.com/docs/get-started/get-connected/mamp)


