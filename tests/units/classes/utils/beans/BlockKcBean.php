<?php
/*
 * Created on Oct 11, 2013
 * Author: Yoni Rosenbaum
 *
 * Note: Many queries against this table will also want to have the 'end_date',
 *       and will therefore use the view: VIEW_WITH_END_DATE_NAME and the extra
 *       column: END_DATE.
 */

class BlockKcBean extends BlockKcBeanBase {
    const VIEW_WITH_END_DATE_NAME = "block_kc_w_end_date";
    const END_DATE = "end_date";
}