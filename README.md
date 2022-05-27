# GNAR LICENSING WORDPRESS PLUGIN

Create and manage Gnar licences

## Features

- create licence upon woocom plugin purchase
    - customer email is sent and stored
    - plugin name is sent and stored
    - wc_subscription status is sent and stored
    - licence key is created api side and returned
    - domain is unknown until activation
    - licence key is added to wc_order_meta & notes

- customer can view licences and change domains from woocom my account page

- upon wc_activation status change to failed/expired api is updated

- admin page display all licences


## To do

- enqueue front end style
- my account page (show all software associated with email (licence key, software, version, download link, domain))
- attach subscription status to licence status