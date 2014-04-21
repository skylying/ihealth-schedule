# iHealth 排程管理 (ihealth-schedule)

## 注意事項

- [Prototype](http://ihealth.prototype.ipharmacy.com.tw/index)
- [Trello](http://trello.com/b/74CnbCQs/ihealth-crm-schedule-ow)

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
