import common from './en/common';
import product from './en/product';
import order from './en/order';
import discount from './en/discount';
import payment from './en/payment';
import auth from './en/auth';
import menu from './en/menu';
import dashboard from './en/dashboard';
import report from './en/report';

const en = {
  common,
  product,
  order,
  discount,
  payment,
  auth,
  menu,
  dashboard,
  report,
} as const;

export default en;
export type Translations = typeof en;
