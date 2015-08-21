MATA CMS User
==========================================

![MATA CMS Module](https://s3-eu-west-1.amazonaws.com/qi-interactive/assets/mata-cms/gear-mata-logo%402x.png)

User module for MATA CMS.


Acknowledgement
------------
This module is based on the excellent [Yii2 User by dektrium](https://github.com/dektrium/yii2-user).


Installation
------------

- Add the module using composer:

```json
"matacms/matacms-user": "~1.0.0"
```

-  Run migrations
```
php yii migrate/up --migrationPath=@vendor/matacms/matacms-user/migrations
```

Changelog
---------

## 1.0.2.1-alpha, August 21, 2015

- Removed delete/block button on list view

## 1.0.2-alpha, August 21, 2015

- Added sending email whe administrator changed user password
- Fixed Block/Unblock functionality
- Added profile fields on create user form
- Updates

## 1.0.1.8-alpha, July 23, 2015

- Updates

## 1.0.1.7-alpha, July 22, 2015

- Updates

## 1.0.1.6-alpha, July 22, 2015

- Updates, role assignment (rbac) field added with functionality

## 1.0.1.5-alpha, July 22, 2015

- Update

## 1.0.1.4-alpha, July 20, 2015

- Updates

## 1.0.1.3-alpha, July 20, 2015

- Update

## 1.0.1.2-alpha, July 20, 2015

- Updates

## 1.0.1.1-alpha, July 20, 2015

- Added dependency on matacms/matacms-rbac ~1.0-alpha


## 1.0.1-alpha, July 20, 2015

- Added dependency on matacms/matacms-rbac dev-development

## 1.0.0-alpha, July 20, 2015

- Initial release
