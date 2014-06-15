# iHealth 排程管理 (ihealth-schedule)

## 注意事項

- [Prototype](http://ihealth.prototype.ipharmacy.com.tw/index)
- [Trello](http://trello.com/b/74CnbCQs/ihealth-crm-schedule-ow)
- [API 文件](https://docs.google.com/document/d/1nhkLdqX7ZH-_5MvIzj_oDtnzwqk3xRe1oQpNQSWFW9Q/edit)

## 專案初始化流程

### Step 1: 先將專案從 GitHub 上 Fork 回來後，clone 回自己的電腦

```bash
$ git clone git@github.com:{your-account}/ihealth-schedule.git
```

### Step 2: Checkout to dev branch

```bash
$ git checkout dev
```

### Step 3: 設定 `configuration.php`

```bash
$ cp configuration.php.dist configuration.php
$ EDITOR configuration.php
```

### Step 4: 複製 `.htaccess`

```bash
$ cp htaccess.dist.txt .htaccess
```

### Step 5: 匯入資料

匯入新專案預設的資訊

```bash
$ php cli/console sql import default schedule fixtures default-user -y
```

## 後台

網址: http://localhost/ihealth-schedule/administrator/index.php?ihealth-schedule

使用預設帳戶登入，帳密: **smstw /  低強度基隆**

## 測試機

網址: http://test.ihealth-schedule.ipharmacy.com.tw/administrator/index.php?ihealth-schedule

## 元件資訊

- com_schedule  
  資料表:
    - `#__schedule_addresses`
    - `#__schedule_colors`
    - `#__schedule_customers`
    - `#__schedule_drugprices`
    - `#__schedule_drugs`
    - `#__schedule_holidays`
    - `#__schedule_hospitals`
    - `#__schedule_images`
    - `#__schedule_institutes`
    - `#__schedule_members`
    - `#__schedule_prescriptions`
    - `#__schedule_routes`
    - `#__schedule_schedules`
    - `#__schedule_senders`
    - `#__schedule_tasks`

## 各大系統編號規則

| 元件 | 範例 | 編號規則 | 備註
| --- | --- | --- | --- |
| 處方箋編號 | P1 | P+id |	1.Bar code會貼在處方箋上  2.可能前面需要補0 |
| 排程編號 | S394-2 | S+id+第幾次配送	後面數字不會超過 3 |
| 機構編號 | (需討論) | 流水號	要跟iCRM、爸媽Home、爸媽CRM同步 |
| 外送藥師編號 | T30 | T+id |
| 客戶編號 | C100 | C+id |
| 會員編號 | M55 | M+id |
| 醫院編號 | H1073 | H+id	|
