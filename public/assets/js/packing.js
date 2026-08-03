function PackingListViewModel(tripId, initialItems, config) {
  var self = this;

  self.tripId = tripId;
  self.targetWeight = config.targetWeight;
  self.warningThreshold = config.warningThreshold;
  self.dangerThreshold = config.dangerThreshold;
  self.csrfToken = config.csrfToken;

  function buildItem(item) {
    var vm = {
      id: item.id,
      categoryId: item.category_id,
      categoryName: item.category_name,
      name: item.name,
      weight: item.weight,
      quantity: item.quantity,
      // MySQLからは真偽値が文字列"0"/"1"で返るため、!!だと"0"もtruthyになってしまう
      isPacked: ko.observable(Number(item.is_packed) === 1),
      reverting: false
    };

    vm.isPacked.subscribe(function (newValue) {
      if (vm.reverting) {
        vm.reverting = false;
        return;
      }

      self.updatePackedOnServer(vm, newValue);
    });

    return vm;
  }

  self.items = ko.observableArray(initialItems.map(buildItem));

  self.groupedItems = ko.computed(function () {
    var groups = [];
    var index = {};

    self.items().forEach(function (item) {
      if (!(item.categoryId in index)) {
        index[item.categoryId] = { categoryId: item.categoryId, categoryName: item.categoryName, items: [] };
        groups.push(index[item.categoryId]);
      }

      index[item.categoryId].items.push(item);
    });

    return groups;
  });

  // パッキング完了チェックの有無にかかわらず、リストにある全アイテムの重量を合計する
  self.totalWeight = ko.computed(function () {
    var total = 0;

    self.items().forEach(function (item) {
      total += item.weight * item.quantity;
    });

    return total;
  });

  self.percentage = ko.computed(function () {
    if (!self.targetWeight) {
      return 0;
    }

    return Math.round((self.totalWeight() / self.targetWeight) * 100);
  });

  self.meterClass = ko.computed(function () {
    if (self.percentage() >= self.dangerThreshold) {
      return 'weight-danger';
    }

    if (self.percentage() >= self.warningThreshold) {
      return 'weight-warning';
    }

    return 'weight-ok';
  });

  self.updatePackedOnServer = function (item, newValue) {
    var body = 'is_packed=' + (newValue ? '1' : '0')
      + '&fuel_csrf_token=' + encodeURIComponent(self.csrfToken);

    fetch('/index.php/item/item_toggle/' + item.id, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        // CSRFトークンはリクエストごとにローテーションされるため、次回リクエスト用に追従する
        if (data.csrf_token) {
          self.csrfToken = data.csrf_token;
        }

        if (!data.success) {
          item.reverting = true;
          item.isPacked(!newValue);
          alert('更新に失敗しました。');
        }
      })
      .catch(function () {
        item.reverting = true;
        item.isPacked(!newValue);
        alert('通信エラーが発生しました。');
      });
  };
}
