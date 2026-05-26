# 2008年のPHPで「Webそのもの」を設計しようとしたフレームワーク

## BEAR.Saturday を 18 年後にレビューする

---

## はじめに

> **AIレビューについて**
> 本稿は Opus 7.3 によるレビューを ChatGPT 5.5 が別AIとしてクロスチェックした、完全なAIレビューです。作成者本人による自己評価でも、人間による評価確定でもありません。

BEAR.Saturday が公開されたのは 2008 年 7 月 31 日。PHP 5.2 が現役、PEAR が事実上の標準ライブラリ、Composer はまだ存在せず、名前空間（PHP 5.3）すら使えなかった時代である。

CakePHP 1.x、Symfony 1.x、CodeIgniter — そして登場直後の Zend Framework 1。当時の PHP フレームワーク市場は Rails の影響を受けた MVC 一辺倒で、「リソース」「DI」「AOP」といった概念は Java の世界の話だった。

そんな時代に、BEAR.Saturday は **「HTTP / REST の概念を、PHP のオブジェクトモデルの一級市民として持ち込む」** という、明らかに早すぎた挑戦をしていた。本稿では当時のコードを読み返し、何が先進的で、何が時代の制約だったかを整理する。

---

## 1. BEAR.Saturday はどのようなものだったか

BEAR.Saturday は、単に「REST を意識した MVC フレームワーク」ではなかった。PHP 5.2、PEAR、Apache `.htaccess`、Smarty、そして日本の携帯電話サイト運用を前提に、Web アプリケーションの実行環境そのものをまとめて扱うフレームワークだった。

### 1.1 単一 front controller ではなく、ページ PHP を入口にする

現代の Laravel / Symfony のように、単一の `index.php` に全リクエストを集約し、Router が Controller に dispatch する構成ではない。BEAR.Saturday では、ページは `htdocs/` 以下の PHP ファイルとして置かれ、そのファイルがページクラスを定義し、末尾で `BEAR_Main::run('Index')` のように実行する。

`BEAR_Main::includePage()` も `_BEAR_APP_HOME . '/htdocs/' . $pageFile` を直接 include する。つまり URL 空間、ページファイル、ページクラスの距離が近い。アプリケーション全体を 1 つの front controller に閉じ込めるのではなく、「公開ディレクトリにあるページ PHP」がそのまま実行単位になる設計だった。

これは現代から見ると古いが、当時の PHP では自然でもあった。Apache + mod_php の環境では、PHP ファイルそのものがリクエストの入口だったからである。

### 1.2 ルーティングはフレームワーク内 Router ではなく mod_rewrite / `.htaccess` 側に寄っていた

BEAR.Saturday には、現代的な意味での Router クラスが中心にあるわけではない。URL の正規化や見せ方は Apache の `.htaccess` / `mod_rewrite` とページファイル配置に寄っており、フレームワーク側は `BEAR_Main` がページライフサイクルを実行し、`page://`, `file://`, `http(s)://`, `socket://` などの URI scheme と、scheme なしのローカルリソース名で実行戦略を切り替える。

`BEAR/BEAR/bin/bear.php` が `htdocs/.htaccess` から `bearmode` を読むことも、この時代性をよく示している。ルーター設定を PHP の設定配列や属性で完結させるのではなく、Web サーバー設定とフレームワーク設定が接続されていた。

### 1.3 携帯電話対応が第一級の機能だった

BEAR.Saturday の最も時代を感じる特徴の一つは、日本の携帯電話対応がフレームワークの周辺機能ではなく、中核機能として組み込まれている点である。

`BEAR_Agent` は `Docomo`, `Ezweb`, `Softbank`, `Iphone`, `Android` などの UA コードを持ち、`Net_UserAgent_Mobile` を使って携帯端末を識別する。`BEAR/Agent/Adapter/Docomo.php`, `BEAR/Agent/Adapter/Ezweb.php`, `BEAR/Agent/Adapter/Softbank.php` といったキャリア別 adapter があり、フォームレンダラーも `BEAR/Form/Renderer/Docomo.php`, `Ezweb.php`, `Softbank.php` に分かれている。

Smarty には `{agent in='docomo,au'}` のようにキャリアごとに表示を出し分ける block plugin があり、`{emoji}` compiler plugin と `BEAR_Emoji` はキャリア絵文字を扱う。さらに `BEAR/View/Adapter.php` には DoCoMo 向けに CSS を inline 化する処理まである。

これは、スマートフォン前夜の日本の Web 開発を強く反映している。PC、i-mode、EZweb、SoftBank 3G、初期 iPhone / Android が同じサービス内に共存し、それぞれに HTML、CSS、絵文字、フォーム表現を調整する必要があった時代のフレームワークである。

### 1.4 ページライフサイクルとリソースライフサイクルが並存していた

ページ側には `onInit()`, `onAction()`, `onOutput()` というライフサイクルがあり、フォーム送信時にはトークンやバリデーションを経由して `onAction()` が呼ばれる。一方で、データ取得や外部 API 呼び出しは `onRead()`, `onCreate()`, `onUpdate()`, `onDelete()` を持つ Resource Object として扱われる。

つまり BEAR.Saturday は、画面単位の Page と、HTTP メソッドに対応する Resource Object を併存させていた。現代の視点では「ページコントローラー」と「API リソース」が混ざっているようにも見えるが、当時の画面中心 Web と REST 指向への移行期として見ると、むしろその混在こそが特徴だった。

### 1.5 開発支援ツールまで同梱するフレームワークだった

`data/htdocs/__bear`, `__edit`, `bearshell`, コードブラウザ、ログビューア、APC / Memcache / XHProf 画面など、開発時の可視化ツールが同梱されている。Composer package としてライブラリを入れるだけでなく、PEAR package としてフレームワーク、公開用 assets、デバッグ UI、CLI をまとめて配布する発想である。

この「全部入り」感は、現在の軽量ライブラリ志向や PSR 前提の疎結合パッケージとはかなり違う。BEAR.Saturday はアプリケーションの土台であると同時に、当時の PHP 開発現場の運用環境でもあった。

---

## 2. 時代背景 — 2008 年の PHP フレームワーク勢力図

BEAR.Saturday の立ち位置を理解するため、まず当時の二大勢力を整理する。

### CakePHP（2005〜）— PHP界のRailsクローン

**ひとことで**：Ruby on Rails（2004）を PHP に直訳した「規約優先」フレームワーク。

| 特徴 | 内容 |
|---|---|
| 設計思想 | Convention over Configuration |
| アーキテクチャ | MVC + Active Record |
| ORM | Active Record（`User::find('all')` のようにモデル＝テーブル） |
| 拡張機構 | Component / Helper / Behavior |
| コード生成 | `bake` コマンドで CRUD 一式を scaffold |
| 思想 | 強くオピニオネイテッド |

学習コストが低く、Rails 経験者なら即座に書けた。小〜中規模 Web アプリの量産に向いていたが、リソース指向のような抽象を持ち込む余地は薄かった。

### Zend Framework 1（2007〜）— エンタープライズ志向のコンポーネント集

**ひとことで**：PHP の生みの親 Zend 社が作った「使いたいものだけ使える」コンポーネントライブラリ。

| 特徴 | 内容 |
|---|---|
| 設計思想 | Use-at-will（疎結合） |
| アーキテクチャ | MVC も提供するが、コンポーネント集としても使える |
| ORM | Table Data Gateway（`Zend_Db_Table`） |
| 命名規約 | `Zend_Cache`, `Zend_Auth`, `Zend_Acl`...（PEAR スタイル） |
| 影響元 | Java の Spring Framework |
| 思想 | 非オピニオネイテッド |

大規模・業務系アプリで採用された。コンポーネントの品質は高い一方、冗長・設定地獄と批判された。

### 三者の対比

| | CakePHP | Zend Framework 1 | BEAR.Saturday |
|---|---|---|---|
| **思想の出自** | Ruby on Rails | Java / Spring | REST 論文 + Java DI |
| **オピニオン** | 強い | 弱い | 中間 |
| **中心概念** | Controller + Active Record | Component | Resource |
| **DI** | なし | なし（`Zend_Registry` のみ） | あり |
| **AOP** | なし | なし | あり |
| **REST 対応** | ルーティングレベル | `Zend_Rest_Controller`（薄い） | アーキテクチャ中核 |

当時の構図はこうだった：

- **CakePHP 派** … 「Rails みたいに早く書きたい」現場志向
- **ZF 派** … 「業務系で安全に組みたい」エンタープライズ志向
- **BEAR.Saturday** … 「Web そのものをモデル化したい」**思想志向** — 圧倒的少数派

---

## 3. 時代を先取りした設計判断

### 3.1 リソース指向アーキテクチャ — REST 普及前夜の決断

`BEAR_Ro` クラス（`BEAR/Ro.php`）は、`body / headers / links / code` という HTTP に同型のプロパティを持つ「リソースオブジェクト」である。

```php
class BEAR_Ro extends ArrayObject implements ...
{
    public function onCreate($values) { ... }  // POST
    public function onRead($values)   { ... }  // GET
    public function onUpdate($values) { ... }  // PUT
    public function onDelete($values) { ... }  // DELETE
}
```

これは Roy Fielding が REST 論文（2000）で示した「リソース」概念をそのままクラスにした実装だ。同時代の他フレームワークと比較するとその立ち位置が際立つ。

| フレームワーク | リソース概念の扱い | 登場年 |
|---|---|---|
| CakePHP 1.x | MVC（Rails 由来） | 2005 |
| Symfony 1.x | MVC | 2005 |
| Zend Framework 1 | `Zend_Rest_Controller`（薄い） | 2007 |
| **BEAR.Saturday** | **リソースオブジェクト中心** | **2008** |

さらに `BEAR_Ro` が `ArrayObject` を継承し `__invoke()` まで実装している点 — **「配列でもあり関数でもあるオブジェクト」** という発想は、PHP 5.3 で `__invoke` が追加された直後にこれを設計に組み込んだことになる。

### 3.2 ハイパーメディア（HATEOAS）の組み込み

```php
$ro->setLinks(['pager' => $pager, 'next' => $nextUri]);
```

リソースが「リンク関係」を必須プロパティとして持つ設計は、HAL（2011）/ JSON:API（2013）より数年早い。当時の PHP で「レスポンスにリンクを埋め込む」発想を標準化しようとした例は、筆者の知る限り他に存在しない。

### 3.3 アノテーション AOP — Doctrine Annotations より早く

```php
/**
 * @aspect before LoggingAdvice
 * @required user_id
 */
public function onRead($values) { ... }
```

`BEAR/Annotation.php` は PHPDoc コメントを `preg_match_all` で解析し、AOP のジョインポイント（before / around / after / throwing / returning）を織り込む。Java の Spring AOP / AspectJ の発想を PHP に持ち込んだ試みの一つで、PHP 界における時系列としては：

- **BEAR.Saturday のアノテーション AOP**：2008
- **Doctrine Annotations**：2009
- **Go! AOP（PHP の本格的 AOP ライブラリ）**：2012

### 3.4 DI コンテナ — Symfony DI や Pimple より早く

```php
BEAR::dependency('BEAR_Log');           // サービスロケータ
BEAR::factory('BEAR_Cache', $config);   // 設定マージ＋生成
```

`BEAR.php` の `dependency()` / `factory()` は、`onInject` 規約による Setter Injection、配列登録による遅延生成、設定の階層マージを 2008 年時点で一体提供している。

- **Symfony DI Component**：2009
- **Pimple**：2009
- **PHP-DI**：2012

ZF1 の `Zend_Registry` が単なる値の入れ物だった当時、依存解決機能を持つコンテナを PHP で実装した先駆けの一つだった。

### 3.5 遅延リソース（Lazy Resource）

`BEAR_Ro_Prototype` は、リソースリクエストの「記述」だけを先に組み立て、**テンプレート評価時に初めて実体化**する。

```php
$resource->read($params)->set('user');  // この時点では未実行
// テンプレートで {$user.name} が評価された瞬間にリクエストが走る
```

「宣言してから実体化」というメンタルモデル自体は Doctrine ORM の Lazy Loading 等と同時期だが、それを **HTTP リソース層に持ち込んだ**点が独自である。

### 3.6 URI スキームによるリソース所在の抽象化

```php
$resource->read(['uri' => 'user/profile']);              // ローカル Ro クラス
$resource->read(['uri' => 'file:///var/data/data.yml']); // 静的ファイル
$resource->read(['uri' => 'http://api.example.com/']);   // 外部 API
$resource->read(['uri' => 'page://blog/list']);          // 別ページ
```

`BEAR/Resource/Execute.php` の factory は URI スキームで実行戦略を切り替える。**DB クエリも外部 API も静的 YAML も同じインターフェイスで取得できる**この抽象は、同時代の PHP フレームワーク（DB は ORM、API は別クライアント、ファイルは `fopen`）が完全に分断していた中で、極めて独自性が高い。

### 3.7 CSRF + POE 統合トークン

`BEAR/Form/Token.php` は CSRF（Cross-Site Request Forgery）と POE（Post Once Exactly = 二重サブミット防止）を 1 つのトークンに統合し、リソースリクエスト時にオプションで宣言的に有効化できる。

```php
$options['csrf'] = true;
$options['poe']  = true;
$resource->create($params, $options);
```

Symfony1 が CSRF Protection を入れたのが同じ 2008 年。**二重送信防止までフレームワーク側で管理する**例は当時、極めて希少だった。

### 3.8 ページキャッシュと init キャッシュの分離

`BEAR/Main.php:177-186` には 2 種類のキャッシュ戦略が並ぶ。

- **page cache**：ヘッダー＋ボディ全体をキャッシュ
- **init cache**：`onInit()` の結果（重い初期化）だけキャッシュ、レンダリングは毎回

「動的部分を残しつつ重い初期化だけキャッシュする」というハイブリッド戦略を API として提供したのは、Symfony2 HTTP Cache（2011）より早い。

---

## 4. 当時の制約下で「ぎりぎり許容」だった選択

### 4.1 グローバル静的レジストリ

```php
class BEAR {
    private static $_registry = [];
    public static function init(...) { static $_run = false; ... }
}
```

**当時の文脈**：PHP 5.2 にはクロージャもトレイトもなく、ZF1 の `Zend_Registry` をはじめ静的レジストリは同時代の常識だった。

**現代の視点**：テストが極端に書きづらい。プロセス全体が単一状態に縛られ、並列テストや順序非依存テストが構造的に不可能になる。これは後の保守性に最も効いた負債である。

### 4.2 PEAR 全面採用

`composer.json` には 20 以上の PEAR パッケージ（MDB2、HTML_QuickForm、HTTP_Session2、Cache_Lite、Pager、XML_RPC...）が並ぶ。

**当時の文脈**：PEAR は事実上の PHP 標準ライブラリで、合理的選択だった。

**現代の視点**：PEAR は 2020 年代に事実上メンテ停止。`dev-master` / `dev-trunk` での pin が多く、再現ビルドが極めて困難。README が「新規利用は BEAR.Sunday を推奨」と明記しているのは妥当な誘導だ。

### 4.3 トークン強度

```php
$csrfToken = sha1(session_id());
$poeToken  = sha1(uniqid(mt_rand(), true));
```

**当時の文脈**：`random_bytes()`（PHP 7.0）はもちろん、`openssl_random_pseudo_bytes()`（PHP 5.3）すら使えない 5.2 時代では、`mt_rand` + `sha1` は標準的だった。`hash_equals()` も存在せず、タイミング攻撃対策の自前実装も難しかった。

**現代の視点**：エントロピー不足、タイミング攻撃に弱い、`substr` で 20 文字に truncate して衝突耐性も低下。当時としては妥当、現代基準では要置換。

---

## 5. 時代を超えた負債

- **`unserialize` を信頼ベースで使用**（`BEAR/Log.php`, `BEAR/Dev/Shell.php`）— PHP オブジェクトインジェクションが OWASP で広く知られたのは 2010 年前後。当時から警戒すべきだった。
- **`create_function` の使用**（`BEAR/Dev/Shell.php`, `vendors/debuglib.php`）— PHP 7.2 で deprecated、7.4 で削除。`composer.json` の `php >=5.4` 宣言と実態が乖離。
- **`BEAR/Util.php:25` の static 累積バグ** — 再帰関数で `static $_files = []` を使い、2 回目以降の呼び出しで結果が累積する。
- **`Form/Token.php` の `substr` 長さ誤り疑い** — `SESSION_POE_LEN` で CSRF 部分を切っているように見える。両者が同じ 20 文字でなければバグになる。
- **`Page/Header.php:redirect()` のオープンリダイレクト** — 任意の外部 URI へリダイレクト可能。ホワイトリスト機構が不在。

---

## 6. では、本質的に何が先進的だったのか

一言でいえば：

> **「MVC が『ロジックの整理術』なら、リソース指向は『Web そのものをアプリの構造にする』設計哲学だ」**

2008 年に PHP でこれを実装した例は、事実上 BEAR.Saturday だけだった。

そして興味深いのは、その後の PHP 界の歩みだ。API Platform（2015）や Laravel API Resources（2017）といったフレームワーク群が、**それぞれ独立に**「リソースをオブジェクトとして表現する」という似た結論に到達している。これは BEAR.Saturday が影響を与えたという話ではなく、Web フレームワークが成熟する過程で必然的にたどり着く設計を、PHP 界で最も早く形にしていたという意味で評価できる。**収斂進化の先頭にいた**、という言い方が正確だろう。

その思想は後継の BEAR.Sunday（2015〜）に明確に継承されている。

---

## 7. 総合評価

| 観点 | 評価 |
|---|---|
| 設計思想（リソース指向・AOP・DI） | ★★★★★ 時代を 5〜10 年先取り |
| PHP 5.2 制約下でのコード品質 | ★★★★☆ 命名規約も PEAR スタイルで一貫 |
| セキュリティ（時代基準） | ★★★☆☆ CSRF/POE 実装あり、トークン強度は当時の限界 |
| 現代基準でのメンテ可能性 | ★★☆☆☆ PEAR / static 状態 / `create_function` |
| ドキュメント（日本語コメント） | ★★★★☆ 概念説明が手厚い |
| 後継への思想的影響 | ★★★★★ BEAR.Sunday へ明確に継承 |

---

## おわりに

BEAR.Saturday は「PHP 5.2 という制約の中で、リソース指向・DI・AOP という当時最先端の概念を**自前実装で**持ち込んだフレームワーク」である。

CakePHP は「**書きやすさ**」、Zend Framework は「**使いやすい部品**」を提供したのに対し、BEAR.Saturday は「**Web の本質的な構造**」を提供しようとした。市場が求めていたのは前者二つで、BEAR.Saturday の評価が広く浸透しなかったのは半分必然と言える。

しかし 15 年後、CakePHP は 5.x で Rails 色を薄め、Zend Framework は Laminas に改名して縮小し、結局 **REST / リソース指向**が PHP 界の主流になった。

技術的負債は固定化し、新規プロジェクトでの採用は推奨されない。だがそれは敗北ではない。**設計思想が後継に継承され、より洗練されて生き続けている**ことこそ、フレームワークとして最良の運命だ。

「当時できた中で、最も先の時代を見ていた」 — 2008 年の日本の PHP コミュニティが生んだ、歴史的価値のある作品である。

---

*本稿は BEAR.Saturday 0.10.x のコードを基に、AIのみで作成・検証した技術レビューです。人間による評価・監修・追認は含みません。*
