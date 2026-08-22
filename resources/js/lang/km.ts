import common from './km/common';
import product from './km/product';
import order from './km/order';
import discount from './km/discount';
import payment from './km/payment';
import auth from './km/auth';
import menu from './km/menu';
import dashboard from './km/dashboard';
import report from './km/report';

const km = {
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

export default km;
