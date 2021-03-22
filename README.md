# Affiliate MLM
**Version:** `1.0`

### About
Affiliate MLM is a WordPress MLM based plugin. It has 8 user levels/ranks on which the users get commissions on each level. The plugin is highly integrated with WooCommerce. The commissions are generated once a WooCommerce product is purchased and payment completed. The commission percentages varies on each level depending on total purchase amount.
###### User Ranks
* Level 1: Distributor 
* Level 2: Sales Representative
* Level 3: Unit Manager
* Level 4: Manager
* Level 5: Senior Manager
* Level 6: Executive Manager
* Level 7: Ass. G. Manager
* Level 8: General Manager

Once a customer registeres to the site, "Distributor" rank will automatically assign to them. After they purchase a certain amount which is $4000 then the user will get the "Sales Representative" rank. An sales representative can add 3 referrals. Once all of his referrals becomes "Sales Representative" individually then the user will get "Unit Manager" rank and thus this rank will get up to "General Manager" rank which is the top most rank.

On every purchase on WooCommerce, the user earns points which is 10% of the total purchase amount. After purchasing $4000 amount of product, the user will earn 400 points. After that, on every purchase his parent users (who referred) will receive certain amount of earning bonuses. The bonus amounts on each level are listed below, also the bonus percentages can be customized from the plugin settings page.
* Level 1: Distributor gets _0%_ earning bonus
* Level 2: Sales Representative _10%_ earning bonus
* Level 3: Unit Manager _7.5%_ earning bonus
* Level 4: Manager _6.5%_ earning bonus
* Level 5: Senior Manager _5.5%_ earning bonus
* Level 6: Executive Manager _4.5%_ earning bonus
* Level 7: Ass. G. Manager _3.5%_ earning bonus
* Level 8: General Manager _2.5%_ earning bonus


### Installation
Clone this repository
```sh
$ git clone git@github.com:ohid/affiliate-mlm.git
```

Now cd into affiliate-mlm
```sh
$ cd affiliate-mlm
```
Install composer-
```sh
$ composer install  
```

### Features
1. Multi-level based plugin
2. Have points and earning bonus feature
3. Child user referral feature
4. Affiliate product sale feature
5. New payment method (Site balance). Users can purchase from the site using their earned bonus.
6. Ability to mobile-bank and bank withdrawal request
7. Referral earning history logs
8. Dashboard for managing withdrawal requests, users and much more...

### Usage
Using the plugin is very simple. As this plugin is highly integrated with WooCommerce, so that is the only required plugin to get started with Affiliate MLM. Most of the features of the plugin is automatic. It doesn't require any further setup. Once the users purchases from the store, they automatically gets bonus points and parent users will get earning bonus value. 

#### Screenshots:
WordPress Dashboard 
[Plugin Dashboard](https://i.imgur.com/yJUb9Hs.png)
[Withdraw Requests](https://i.imgur.com/m2IHx2B.png)
[Single Withdraw Action](https://i.imgur.com/Wk7axG0.png)
[All Members](https://i.imgur.com/zIOVNV1.png)
[Settings](https://i.imgur.com/pha898w.png)

Front-end on "My Account" page
[My Account Customzied Dashboard](https://i.imgur.com/LMwSpzl.png)
[Referral Users](https://i.imgur.com/aZdl3CE.png)
[Referral Earning History Logs](https://i.imgur.com/Yinwxg0.png)
[Withdrawal Page](https://i.imgur.com/LVdKG3n.png)